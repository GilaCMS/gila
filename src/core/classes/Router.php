<?php

class Router
{
  static private $args = [];
  static $url;
  static $caching = false;
  static $caching_file;
  static $controllers = [];
  static $actions = [];
  static $method;
  static $controller;
  static $action;
  static private $route = [];

  function __construct ()
  {
    self::run($_GET['url'] ?? false);
  }

  static function run ($_url = false)
  {
    global $c;

    self::$method = $_SERVER['REQUEST_METHOD'];
    if(isset(Gila::$route[$_url])) {
      Gila::$route[$_url]();
      return;
    }
    foreach(self::$route as $route) if($route[0]===self::$method && $route[1]===$_url) {
      $route[2]();
      return;
    }

    self::setUrl($_url);

    $controller = self::getController();
    $ctrlPath = self::$controllers[$controller];
    $ctrlClass = substr($ctrlPath, strrpos($ctrlPath, '/' )+1);
    require_once('src/'.$ctrlPath.'.php');
    $action = self::getAction($ctrlClass);

    if($action === '') {
      @http_response_code(404);
      return;
    }

    if($ctrlClass==='blog') $ctrlClass='Blog'; // DEPRECIATED
    $c = new $ctrlClass();

    // find function to run after controller construction
    if(isset(Gila::$on_controller[$controller]))
      foreach(Gila::$on_controller[$controller] as $fn) $fn();

    $action_fn = $action.'Action';
    $action_m = $action_fn.ucwords(self::$method);

    if(isset(Gila::$before[$controller][$action])) {
      foreach(Gila::$before[$controller][$action] as $fn) $fn();
    }
    if(isset(self::$actions[$controller][$action])) {
      @call_user_func_array (self::$actions[$controller][$action], self::$args);
    } else if(method_exists($c, $action_m)) {
      @call_user_func_array([$c, $action_m], self::$args);
    } else if(method_exists($c, $action_fn)) {
      @call_user_func_array([$c, $action_fn], self::$args);
    } else {
      @http_response_code(404);
    }

    // end of response
    if(self::$caching===true) {
      $out2 = ob_get_contents();
      $clog = new Logger(LOG_PATH.'/cache.log');
      if(!file_put_contents(self::$caching_file, $out2)){
        $clog->error(self::$caching_file);
      }
    }
  }

  static function getController ():string
  {
    if(isset(self::$controller)) return self::$controller;
    $args = &self::$args;
    $default = Gila::config('default-controller');
    $controller = self::request('c',$default);

    if (isset($args[0])) {
      if(isset(Gila::$controller[$args[0]])) {
        $controller = $args[0];
        array_shift($args);
        self::$controllers[$controller] = Gila::$controller[$controller];
      } else if(isset(self::$controllers[$args[0]])) {
        $controller = $args[0];
        array_shift($args);
      }
    }

    if ($controller===$default && !isset(self::$controllers[$default])) {
      // default-controller not found so have to reset on config.php file
      $controller = 'admin';
      Gila::config('default-controller','admin');
      Gila::updateConfigFile();
    }

    self::$controller = $controller;
    return $controller;
  }

  static function getAction($ctrClass = null):string
  {
    if(isset(self::$action)) return self::$action;
    $args = &self::$args;
    $action = self::request('action', @$args[0]?:'index');

    if (!method_exists($ctrClass,$action.'Action') &&
        !isset(self::$actions[self::getController()][$action])) {
      if (method_exists($ctrClass,'indexAction')) {
        $action = $args[0] ? 'index' : 'index';
      } else {
        $action = '';
      }
    }

    if(isset($args[0]) && $args[0]===$action) {
      array_shift($args);
    }
    
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
  static function get ($string, $fn = null)
  {
    if(is_int($fn) || $fn===null) { // DEPRECIATED
      return self::param($string, $fn);      
    } else {
      self::add('get', $string, $fn);
    }
  }

  static function add ($method, $string, $fn)
  {
    self::$route[] = [$method, $string, $fn];
  }

  static function param ($key, $n = null)
  {
    if ($n!==null && isset(self::$args[$n-1]) && self::$args[$n-1]!==null) {
      return self::$args[$n-1];
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
  static function controller ($name = null, $path = null)
  {
    if($name===null) {
      return @self::getController(); // DEPRECIATED
    }
    self::$controllers[$name] = $path;
  }

  /**
  * Returns the name of the action
  */
  static function action ($set = null)
  {
    if($set) self::$action = $set; // DEPRECIATED 
    return @self::getAction(self::getController(), self::$args);
  }

  static function args_shift() // DEPRECIATED used only from controllers/api
  {
    array_shift(self::$args);
  }

  static function setUrl($_url) {
    if($_url!==false) {
      self::$url = strip_tags($_url);
      self::$args = explode("/", self::$url);
    }
    else {
      self::$url = false;
      self::$args = [];
    }
  }

  static function cache ($time = 3600, $args = null, $uniques = null) {
    if ($_SERVER['REQUEST_METHOD']!=="GET") {
      // only for get requests
      return;
    }
    if(isset(View::$canonical)) {
      $request_uri = View::$canonical;
    } else {
      $request_uri = strtok($_SERVER['REQUEST_URI'], '?');
    }

    $dir = Gila::dir(LOG_PATH.'/cache0/');
    self::$caching_file = $dir.str_replace(['/','\\'],'_',$request_uri);
    if($args !== null) self::$caching_file .= '|'.implode('|',$args);
    if($uniques !== null) {
      $pre_unique = self::$caching_file;
      self::$caching_file .= '|'.implode('|',$uniques);
    }
    if(file_exists(self::$caching_file) && filemtime(self::$caching_file)+$time>time()) {
      if(sizeof($_GET)>1) return;
      $controller = self::getController();
      $action = self::getAction();
      if(isset(Gila::$onaction[$controller][$action])) {
        foreach(Gila::$onaction[$controller][$action] as $fn) $fn();
      }
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
