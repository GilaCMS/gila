<?php

class Router
{
  static private $args = [];
  static $url;
  static $caching = false;
  static $caching_file;
  static private $controller;
  static private $action;

  function __construct ()
  {
    self::run($_GET['url'] ?? false);
  }

  static function run ($_url = false)
  {
    global $c;

    if(isset(Gila::$route[$_url])) {
      Gila::$route[$_url]();
      return;
    }

    if($_url!==false) {
      Router::$url = strip_tags($_url);
      Router::$args = explode("/", Router::$url);
    }
    else {
      Router::$url = false;
      Router::$args = [];
    }

    $controller = Router::get_controller(Router::$args);
    $controller_file = 'src/'.Gila::$controller[$controller].'.php';

    if(!file_exists($controller_file)) {
      @trigger_error("Controller could not be found: $controller=>$controller_file", E_NOTICE);
      exit;
    }

    require_once $controller_file;

    $controllerClass = $controller;
    if(isset(Gila::$controllerClass[$controller])) {
      $controllerClass = Gila::$controllerClass[$controller];
    }
    $c = new $controllerClass();

    // find function to run after controller construction
    if(isset(Gila::$on_controller[$controller]))
      foreach(Gila::$on_controller[$controller] as $fn) $fn();

    $action = Router::get_action($controllerClass, Router::$args);
    $action_fn = $action.'Action';

    if(isset(Gila::$before[$controller][$action]))
      foreach(Gila::$before[$controller][$action] as $fn) $fn();
    if(isset(Gila::$action[$controller][$action])) {
      @call_user_func_array (Gila::$action[$controller][$action], Router::$args);
    } else {
      @call_user_func_array ([$c, $action_fn], Router::$args);
    }

    // end of response
    if(self::$caching) {
      $out2 = ob_get_contents();
      $clog = new Logger(LOG_PATH.'/cache.log');
      if(!file_put_contents(self::$caching_file,$out2)){
        $clog->error(self::$caching_file);
      }
    }
  }

  static function get_controller (&$args):string
  {
    if(isset(self::$controller)) return self::$controller;
    $default = Gila::config('default-controller');
    $controller = Router::request('c',$default);

    if (isset($args[0])) {
      if(isset(Gila::$controller[$args[0]])) {
        $controller = $args[0];
        array_shift($args);
      }
    }

    if ($controller==$default && !isset(Gila::$controller[$default])) {
      // default-controller not found so have to reset on config.php file
      $controller = 'admin';
      Gila::config('default-controller','admin');
      Gila::updateConfigFile();
    }

    self::$controller = $controller;
    return $controller;
  }

  static function get_action(&$controller,&$args):string
  {
    if(isset(self::$action)) return self::$action;
    $action = self::request('action',@$args[0]?:'index');

    if (!method_exists($controller,$action.'Action')) {
      if (method_exists($controller,'indexAction')) {
        $action = 'index';
      } else {
        $action = '';
      }
    }

    if(isset($args[0]) && $args[0]==$action)
      array_shift($args);

    $action = explode('.', $action);
    self::$action = $action[0];
    return self::$action;
  }

  /**
  * Returns a get parameter value
  * @param $key (string) Parameter's name
  * @param $n optional (int) Parameter's expected position in a pretty url.
  * @return Parameter's value or null if paremeter is not found.
  */
  static function get ($key, $n = null)
  {
    if ((isset(Router::$args[$n-1])) && ($n != null) && (Router::$args[$n-1]!=null)){
      return Router::$args[$n-1];
    }
    else if (isset($_GET[$key])) {
      return $_GET[$key];
    }
    else if (isset($_GET['var'.$n])) {
      return $_GET['var'.$n];
    }
    else {
      return null;
    }
  }

  /**
  * Returns the value of a post parameter
  * @param $key (string) Parameter's name
  * @return null if the parameter is not set
  */
  static function post ($key,$default=null)
  {
    return isset($_POST[$key])?$_POST[$key]:$default;
  }

  static function request ($key,$default=null)
  {
    $r = $_REQUEST[$key] ?? $default;
    return @strip_tags($r);
  }

  static function url ()
  {
    return self::$url;
  }

  /**
  * Returns the name of the controller
  */
  static function controller ()
  {
    return @Router::get_controller(self::$args);
  }

  /**
  * Returns the name of the action
  */
  static function action ($set = null)
  {
    if($set) self::$action = $set;
    return @Router::get_action(self::controller(),self::$args);
  }

  static function args_shift()
  {
    array_shift(self::$args);
  }

  static function cache ($time = 3600, $args = null, $uniques = null) {
    if(isset(View::$canonical)) {
      $request_uri = View::$canonical;
    } else {
      $request_uri = $_SERVER['REQUEST_URI'];
    }

    $dir = Gila::dir(LOG_PATH.'/cache0/');
    self::$caching_file = $dir.str_replace(['/','\\'],'_',$request_uri);
    if($args !== null) self::$caching_file .= '|'.implode('|',$args);
    if($uniques !== null) {
      $pre_unique = self::$caching_file;
      self::$caching_file .= '|'.implode('|',$uniques);
    }
    if(file_exists(self::$caching_file) && filemtime(self::$caching_file)+$time>time()) {
      if(sizeof($_REQUEST)>1) return;
      include self::$caching_file;
      exit;
    } else {
      if($uniques !== null) {
        array_map('unlink', glob($pre_unique.'*'));
      }
      ob_start();
      self::$caching = true;
    }
  }

}
