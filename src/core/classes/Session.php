<?php

use core\models\user;

class Session
{
  private static $started = false;
  private static $waitForLogin = 0;
  private static $user_id;

  static function start ()
  {
    if(self::$started==true) return;
    self::$started = true;
    session_set_cookie_params(24*3600);

    try {
      @session_start();
    } catch (Exception $e) {
      trigger_error($e->getMessage());
    }
    Session::define(['user_id'=>0]);

    if (isset($_POST['username']) && isset($_POST['password']) && Session::waitForLogin()==0) {
      $usr = User::getByEmail($_POST['username']);
      if ($usr && $usr['active']==1 && password_verify($_POST['password'], $usr['pass'])) {
        Session::user($usr['id'], $usr['username'], $usr['email'], 'Log In');
        unset($_SESSION['failed_attempts']);
      } else {
        @$_SESSION['failed_attempts'][] = time();
        $session_log = new Logger(LOG_PATH.'/login.failed.log');
        $session_log->log($_SERVER['REQUEST_URI'], htmlentities($_POST['username']));
      }
    } else {
      if(Session::user_id()===0) {
        if(isset($_COOKIE['GSESSIONID'])) {
          foreach (User::getIdsByMeta('GSESSIONID', $_COOKIE['GSESSIONID']) as $user_id) {
            $usr = User::getById($user_id);
            if ($usr && $usr['active']==1) {
              Session::user($usr['id'], $usr['username'], $usr['email']);
            }
          }
        }
      } else {
        if(!isset($_COOKIE['GSESSIONID'])) {
          self::setCookie (Session::user_id());
        }
      }

      if(isset($_COOKIE['GSESSIONID'])) if(!file_exists(LOG_PATH.'/sessions/'.$_COOKIE['GSESSIONID'])) {
        User::metaDelete(Session::user_id(), 'GSESSIONID', $_COOKIE['GSESSIONID']);
        Session::destroy();
      }
    }

  }

  static function user ($id, $name, $email, $msg=null)
  {
    Session::key('user_id', $id);
    Session::key('user_name', $name);
    Session::key('user_email', $email);
    self::$user_id = $id;
    if($msg!==null) {
      $session_log = new Logger(LOG_PATH.'/sessions.log');
      $session_log->info($msg,['user_id'=>$id, 'email'=>$email]);
    }
    self::setCookie($id);
  }

  static function setCookie ($id) {
    $chars = 'bcdfghjklmnprstvwxzaeiou123467890';
    $gsession = (string)$id;
    for ($p = strlen($gsession); $p < 50; $p++) $gsession .= $chars[mt_rand(0, 32)];
    User::meta($id, 'GSESSIONID', $gsession, true);
    setcookie('GSESSIONID', $gsession, time()+(86400 * 30), '/');
    $expires = date('D, d M Y H:i:s', time() + (86400 * 30));
    if(isset($_COOKIE['GSESSIONID'])) {
      User::metaDelete($id, 'GSESSIONID', $_COOKIE['GSESSIONID']);
    } else {
      // setcookie('GSESSIONID', $gsession, time() + (86400 * 30),
      //  '/', null, null, true, ['samesite'=>'Strict']);
      header("Set-cookie: GSESSIONID=$gsession; expires=$expires; path=/; HttpOnly; SameSite=Strict;");
    }
    $data = [
      'user_agent'=>$_SERVER['HTTP_USER_AGENT']
    ];
    file_put_contents(LOG_PATH.'/sessions/'.$gsession, json_encode($data));
  }

  /**
  * Define new session variables
  * @param $vars Associative Array
  */
  static function define ($vars)
  {
    self::start();
    foreach ($vars as $k=>$v) if(!isset($_SESSION[Session::md5($k)])) {
      $_SESSION[Session::md5($k)]=$v;
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
    if ($val == null) {
      return $_SESSION[Session::md5($var)]?? null;
    }
    $_SESSION[Session::md5($var)] = $val;

    if($t !== 0){
      if(is_object($val) || is_array($val)){
      $value = json_encode($val);
      }
      setcookie(Session::md5($var), $val, (time() + $t));
    }
  }

  /**
  * Unsets a session variable
  * @param $var (string) Variable name
  */
  static function unsetKey ($var)
  {
    self::start();
    unset($_SESSION[Session::md5($var)]);
  }

  private static function md5 ($key)
  {
    $dbname = $GLOBALS['config']['db']['name'];
    return md5($dbname.$key);
  }

  /**
  * Returns user id
  * @return int User's id. 0 if user is not logged in.
  */
  static function user_id ()
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
      if(isset($_COOKIE['GSESSIONID']) || $_SERVER['REQUEST_METHOD']=='GET') {
        @$user_id = $_SESSION[Session::md5('user_id')];
      }
    }
    self::$user_id = $user_id;
    return self::$user_id;
  }

  /**
  * Destroys the session session
  */
  static function destroy ()
  {
    if(self::user_id()>0) {
      $session_log = new Logger(LOG_PATH.'/sessions.log');
      $session_log->info('End',['user_id'=>self::user_id(), 'email'=>self::key('user_email')]);
    }
    @$_SESSION = [];
    @session_destroy();
  }

  static function waitForLogin()
  {
    $wait = 0;
    Session::define(['failed_attempts'=>[]]);
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
