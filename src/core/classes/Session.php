<?php

namespace Gila;

class Session
{
  private static $started = false;
  private static $waitForLogin = 0;
  private static $user_id;

  public static function start()
  {
    if (self::$started===true) {
      return;
    }
    self::$started = true;
    @session_set_cookie_params(86400);

    // token authentication
    if (!isset($_COOKIE['GSESSIONID'])) {
      self::$user_id = 0;
      $token = $_REQUEST['token'] ?? ($_SERVER['HTTP_TOKEN'] ?? null);
      if ($token) {
        User::getByMeta('token', $token);
        if ($usr) {
          self::$user_id = $usr['id'];
        }
      }
      self::login();
      return;
    }

    // load/create the session file
    session_name('GSESSIONID');
    session_start();
    self::$user_id = self::key('user_id') ?? 0;
    if (self::$user_id > 0) {
      // check expiration
      return;
    }

    // verify that session is in database
    if ($session = self::find($_COOKIE['GSESSIONID'])) {
      $usr = User::getById($session['user_id']);
      if ($usr['active']===1) {
        self::user($usr['id'], $usr['username'], $usr['email']);
        self::update($_COOKIE['GSESSIONID']);
        return;
      }
    } else {
      @session_destroy();
    }

    @session_commit();
    self::login();
  }

  public static function login()
  {
    if (isset($_POST['username']) && isset($_POST['password']) && self::waitForLogin()===0) {
      session_start();

      $usr = User::getByEmail($_POST['username']);
      if ($usr && $usr['active']===1 && password_verify($_POST['password'], $usr['pass'])) {
        unset($_SESSION['failed_attempts']);
        @session_destroy();
        @session_commit();
        @session_name('GSESSIONID');
        @session_start();
        self::setCookie($usr['id']);
        self::user($usr['id'], $usr['username'], $usr['email'], 'Log In');
        @session_commit();
        return;
      } else {
        @$_SESSION['failed_attempts'][] = time();
        $session_log = new Logger(LOG_PATH.'/login.failed.log');
        $session_log->log($_SERVER['REQUEST_URI'], htmlentities($_POST['username']));
        session_commit();
      }
    }
  }

  public static function user($id, $name='', $email='', $msg=null)
  {
    self::key('user_id', $id);
    self::key('user_name', $name);
    self::key('user_email', $email);
    self::$user_id = $id;
    if ($msg!==null) {
      $session_log = new Logger(LOG_PATH.'/sessions.log');
      $session_log->info($msg, ['user_id'=>$id, 'email'=>$email]);
    }
  }

  public static function find($gsessionId)
  {
    global $db;
    $res = $db->read()->get("SELECT * FROM sessions
    WHERE gsessionid=? LIMIT 1;", [$gsessionId]);
    return $res[0] ?? null;
  }

  public static function findByUserId($userId)
  {
    global $db;
    return $db->read()->get("SELECT * FROM sessions WHERE user_id=?;", [$userId]);
  }

  public static function update($gsessionId)
  {
    global $db;
    $ql = "UPDATE sessions SET updated=NOW() WHERE gsessionid=?;";
    $db->query($ql, [$gsessionId]);
  }

  public static function setCookie($userId)
  {
    do {
      $gsession = '';
      while (strlen($gsession) < 60) {
        $gsession .= hash('sha512', uniqid(true));
      }
      $gsession = substr($gsession, 0, 60);
    } while (self::find($gsession)!==null);

    setcookie('GSESSIONID', $gsession, [
      'expires' => time() + 86400+30,
      'path' => '/',
      'secure' => Config::get('secure_cookie')??false,
      'httponly' => true,
      'samesite' => 'Strict',
    ]);

    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $ip = $_SERVER['REMOTE_ADDR'];
    self::create($userId, $gsession, $ip, $user_agent);
  }

  public static function create($userId, $gsessionId, $ip, $user_agent)
  {
    global $db;
    
    Event::fire('Session::create', [
      'user_id'=>$userId, 'gsessionid'=>$gsessionId,
      'ip_address'=>$ip, 'user_agent'=>$user_agent
    ]);
    $ql = "INSERT into sessions (user_id, gsessionid, ip_address, user_agent) VALUES (?,?,?,?);";
    $db->query($ql, [$userId, $gsessionId, $ip, $user_agent]);
  }

  public static function remove($gsessionId)
  {
    global $db;
    $ql = "DELETE FROM sessions WHERE gsessionid=?;";
    $db->query($ql, [$gsessionId]);
    @session_commit();
    // destroy other session
    @session_id($gsessionId);
    @session_start();
    @session_destroy();
    @session_commit();
    // restore current session
    @session_id($_COOKIE['GSESSIONID']);
    @session_start();
    @session_commit();
  }


  /**
  * Define new session variables
  * @param $vars Associative Array
  */
  public static function define($vars)
  {
    self::start();
    foreach ($vars as $var=>$val) {
      $key = $GLOBALS['config']['db']['name'].$var;
      if (!isset($_SESSION[$key])) {
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
  public static function key($var, $val = null, $t = 0)
  {
    self::start();
    $key = $GLOBALS['config']['db']['name'].$var;
    if ($val === null) {
      return $_SESSION[$key]?? null;
    }
    $_SESSION[$key] = $val;

    if ($t !== 0) {
      if (is_object($val) || is_array($val)) {
        $value = json_encode($val);
      }
      setcookie($var, $val, (time() + $t));
    }
  }

  /**
  * Unsets a session variable
  * @param $var (string) Variable name
  */
  public static function unsetKey($var)
  {
    self::start();
    $key = $GLOBALS['config']['db']['name'].$var;
    unset($_SESSION[$key]);
  }

  private static function md5($key)
  {
    $dbname = $GLOBALS['config']['db']['name'];
    return $dbname.$key;
  }

  /**
  * Returns user id
  * @return int User's id. 0 if user is not logged in.
  */
  public static function userId():int
  {
    if (!isset(self::$user_id)) {
      self::start();
    }
    return self::$user_id;
  }

  /**
  * Destroys the session session
  */
  public static function destroy()
  {
    if (self::userId()>0) {
      $session_log = new Logger(LOG_PATH.'/sessions.log');
      $session_log->info('End', ['user_id'=>self::userId(), 'email'=>self::key('user_email')]);
    }
    self::remove($_COOKIE['GSESSIONID']);
    @$_SESSION = [];
    @session_destroy();
  }

  public static function waitForLogin()
  {
    $wait = 0;
    self::define(['failed_attempts'=>[]]);
    if (@$_SESSION['failed_attempts']) {
      foreach ($_SESSION['failed_attempts'] as $key=>$value) {
        if ($value+120<time()) {
          array_splice($_SESSION['failed_attempts'], $key, 1);
        }
      }
      $attempts = count($_SESSION['failed_attempts']);
      if ($attempts<5) {
        return 0;
      }
      $lastTime = $_SESSION['failed_attempts'][$attempts-1];
      $wait = $lastTime-time()+60;
      $wait = $wait<0? 0: $wait;
    }
    return $wait;
  }

  public static function hasPrivilege($pri)
  {
    if (!is_array($pri)) {
      $pri=explode(' ', $pri);
    }
    if (!isset($GLOBALS['user_privileges'])) {
      $GLOBALS['user_privileges'] = User::permissions(Session::userId());
    }

    foreach ($pri as $p) {
      if (@in_array($p, $GLOBALS['user_privileges'])) {
        return true;
      }
    }
    return false;
  }
}
