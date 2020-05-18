<?php

use core\models\User;

class Session
{
  private static $started = false;
  private static $waitForLogin = 0;
  private static $user_id;

  static function start ()
  {
    if(self::$started===true) return;
    self::$started = true;
    @session_set_cookie_params(86400); 

    try {
      @session_start();
    } catch (Exception $e) {
      trigger_error($e->getMessage());
    }
    self::define(['user_id'=>0]);

    if (isset($_POST['username']) && isset($_POST['password']) && self::waitForLogin()===0) {
      $usr = User::getByEmail($_POST['username']);
      if ($usr && $usr['active']===1 && password_verify($_POST['password'], $usr['pass'])) {
        self::user($usr['id'], $usr['username'], $usr['email'], 'Log In');
        self::setCookie($usr['id']);
        unset($_SESSION['failed_attempts']);
      } else {
        @$_SESSION['failed_attempts'][] = time();
        $session_log = new Logger(LOG_PATH.'/login.failed.log');
        $session_log->log($_SERVER['REQUEST_URI'], htmlentities($_POST['username']));
      }
    } else {
      if(self::userId()==0) {
        if(isset($_COOKIE['GSESSIONID'])) {
          $user_ids = User::getIdsByMeta('GSESSIONID', $_COOKIE['GSESSIONID']);
          if(isset($user_ids[0])) {
            $usr = User::getById($user_ids[0]);
            if ($usr['active']===1) {
              self::user($usr['id'], $usr['username'], $usr['email']);
            }
          } else {
            @unlink(LOG_PATH.'/sessions/'.$_COOKIE['GSESSIONID']);
          }
        }
      } else {
        if(!isset($_COOKIE['GSESSIONID'])) {
          self::setCookie(self::userId());
        }
      }

      if(isset($_COOKIE['GSESSIONID'])) if(!file_exists(LOG_PATH.'/sessions/'.$_COOKIE['GSESSIONID'])) {
        User::metaDelete(self::userId(), 'GSESSIONID', $_COOKIE['GSESSIONID']);
        self::destroy();
      }
    }

  }

  static function user ($id, $name, $email, $msg=null)
  {
    self::key('user_id', $id);
    self::key('user_name', $name);
    self::key('user_email', $email);
    self::$user_id = $id;
    if($msg!==null) {
      $session_log = new Logger(LOG_PATH.'/sessions.log');
      $session_log->info($msg,['user_id'=>$id, 'email'=>$email]);
    }
  }

  static function setCookie ($id) {
    $chars = 'bcdfghjklmnprstvwxzaeiou123467890';
    $gsession = (string)$id;
    for ($p = strlen($gsession); $p < 50; $p++) $gsession .= $chars[mt_rand(0, 32)];
    $expires = date('D, d M Y H:i:s', time() + (86400 * 30));
    if(isset($_COOKIE['GSESSIONID'])) {
      User::metaDelete($id, 'GSESSIONID', $_COOKIE['GSESSIONID']);
      @unlink(LOG_PATH.'/sessions/'.$_COOKIE['GSESSIONID']);
    }
    header("Set-cookie: GSESSIONID=$gsession; expires=$expires; path=/; HttpOnly; SameSite=Strict;");
    User::meta($id, 'GSESSIONID', $gsession, true);
    self::createFile($gsession);
  }

  /**
  * Define new session variables
  * @param $vars Associative Array
  */
  static function define ($vars)
  {
    self::start();
    foreach ($vars as $var=>$val) {
      $key = $GLOBALS['config']['db']['name'].$var;
      if(!isset($_SESSION[$key])) {
        $_SESSION[$key] = $val;
      }
    }
  }

  /**
  * Returns or sets value for a session variable
  * @param $var (string) Variable name
  * @param $val (optional) Variable value, if is not set the function will return the current value
  * @param $t optional (int) Time in seconds to save the variable in the cookie (not saved if not set)
  * @return Variable value
  */
  static function key ($var, $val = null, $t = 0)
  {
    self::start();
    $key = $GLOBALS['config']['db']['name'].$var;
    if ($val === null) {
      return $_SESSION[$key]?? null;
    }
    $_SESSION[$key] = $val;

    if($t !== 0){
      if(is_object($val) || is_array($val)){
      $value = json_encode($val);
      }
      setcookie($var, $val, (time() + $t));
    }
  }

  /**
  * Unsets a session variable
  * @param $var (string) Variable name
  */
  static function unsetKey ($var)
  {
    self::start();
    $key = $GLOBALS['config']['db']['name'].$var;
    unset($_SESSION[$key]);
  }

  private static function md5 ($key)
  {
    $dbname = $GLOBALS['config']['db']['name'];
    return $dbname.$key;
  }

  /**
  * Returns user id
  * @return int User's id. 0 if user is not logged in.
  */
  static function userId ()
  {
    if(isset(self::$user_id)) return self::$user_id;
    $user_id = 0;
    $token = $_REQUEST['token'] ?? ($_SERVER['HTTP_TOKEN'] ?? null);
    if($token && !isset($_COOKIE['GSESSIONID'])) {
      $usr = User::getByMeta('token', $token);
      if($usr) {
        $user_id = $usr['id'];
      }
    } else {
      self::start();
      if(isset($_COOKIE['GSESSIONID']) || $_SERVER['REQUEST_METHOD']==='GET') {
        @$user_id = self::key('user_id');
      }
      if(isset($_COOKIE['GSESSIONID']) &&
          !file_exists(LOG_PATH.'/sessions/'.$_COOKIE['GSESSIONID'])) {
        self::createFile($_COOKIE['GSESSIONID']);
      }
    }
    self::$user_id = $user_id;
    return self::$user_id;
  }

  static function user_id() { // DEPRECIATED
    trigger_error(__METHOD__.' should be called in camel case', E_USER_WARNING);
    return self::userId();
  }

  static function createFile($gsession) {
    $data = [
      'user_agent'=>$_SERVER['HTTP_USER_AGENT']
    ];
    file_put_contents(LOG_PATH.'/sessions/'.$gsession, json_encode($data));
  }

  /**
  * Destroys the session session
  */
  static function destroy ()
  {
    if(self::userId()>0) {
      $session_log = new Logger(LOG_PATH.'/sessions.log');
      $session_log->info('End',['user_id'=>self::userId(), 'email'=>self::key('user_email')]);
    }
    @unlink(LOG_PATH.'/sessions/'.$_COOKIE['GSESSIONID']);
    @$_SESSION = [];
    @session_destroy();
  }

  static function waitForLogin()
  {
    $wait = 0;
    self::define(['failed_attempts'=>[]]);
    if(@$_SESSION['failed_attempts']) {
      foreach($_SESSION['failed_attempts'] as $key=>$value) {
        if($value+120<time()) array_splice($_SESSION['failed_attempts'], $key, 1);
      }
      $attempts = count($_SESSION['failed_attempts']);
      if($attempts<5) return 0;
      $lastTime = $_SESSION['failed_attempts'][$attempts-1];
      $wait = $lastTime-time()+60;
      $wait = $wait<0? 0: $wait;
    }
    return $wait;
  }

}
