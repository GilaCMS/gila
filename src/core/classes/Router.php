<?php

class Router
{
  static private $args = [];
  static $url;
  static $caching = false;
  static $caching_file;
  static $controllers = [];
  static $on_controller = [];
  static $actions = [];
  static $before = [];
  static $onaction = [];
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
    self::setUrl($_url);
    if(self::matchRoutes(self::$route)==true){
      return;
    }

    $controller = self::getController();
    $ctrlPath = self::$controllers[$controller];
    $ctrlClass = substr($ctrlPath, strrpos($ctrlPath, '/' )+1);
    require_once('src/'.$ctrlPath.'.php');
    $action = self::getAction($ctrlClass);

    if($action === '') {
      @http_response_code(404);
      return;
    }

    if($ctrlClass==='blog') $ctrlClass='Blog'; // DEPRECATED
    $c = new $ctrlClass();

    // find function to run after controller construction
    if(isset(self::$on_controller[$controller]))
      foreach(self::$on_controller[$controller] as $fn) $fn();

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

  }

  static function getController ():string
  {
    if(isset(self::$controller)) return self::$controller;
    $default = Gila::config('default-controller');
    self::$controller = self::request('c',$default);

    if (isset(self::$args[0]) && isset(self::$controllers[self::$args[0]])) {
      self::$controller = self::$args[0];
      array_shift(self::$args);
    }
    if (!isset(self::$controllers[self::$controller])) {
      self::$controller = 'admin';
    }
    return self::$controller;
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
    if(is_int($fn) || $fn===null) { // DEPRECATED
      return self::param($string, $fn);      
    } else {
      self::add($string, $fn);
    }
  }

  static function add ($string, $fn, $method = 'GET', $permission = null)
  {
    self::$route[] = [$string, $fn, $method, $permission];
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

  static function url () // DEPRACATED
  {
    return self::path();
  }
  static function path ()
  {
    return self::$url;
  }

  /**
  * Returns the name of the controller
  */
  static function controller ($name = null, $path = null)
  {
    if($name===null) {
      return @self::getController(); // DEPRECATED
    }
    self::$controllers[$name] = $path;
  }

  /**
  * Returns the name of the action
  */
  static function action ($c=null, $action=null, $fn=null)
  {
    if($fn!==null) {
      Router::$actions[$c][$action] = $fn;
      return;
    }
    if($action===null && $set!==null) self::$action = $set; // DEPRECATED
    return @self::getAction(self::getController(), self::$args); // DEPRECATED
  }

  static function before($c, $action, $fn)
  {
    self::$before[$c][$action][] = $fn;
  }

  static function onAction($c, $action, $fn)
  {
    self::$onaction[$c][$action][] = $fn;
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

  static function cache ($time = 3600, $args = null, $uniques = null) { // DEPRECATED
    if ($_SERVER['REQUEST_METHOD']!=="GET") {
      // only for get requests
      return;
    }
    if(isset(View::$canonical)) {
      $request_uri = View::$canonical;
    } else {
      $request_uri = strtok($_SERVER['REQUEST_URI'], '?');
    }
    if($args !== null) $request_uri .= '|'.implode('|',$args);
    Cache::page($request_uri, $time, $uniques);
  }

  static function matchRoutes(&$routes) {
    $matched = false;
    foreach($routes as $route) {
      if(preg_match('#^'.$route[0].'$#', self::$url,$matches)) {
        $matched = true;
        if(self::$method == $route[2]) {
          if($route[3]!==null && Session::hasPrivilege($route[3])===false) {
            @http_response_code(403);
          } else {
            array_shift($matches);
            call_user_func_array($route[1], $matches);
          }
          return true;
        }
      }
    }
    if($matched) {
      @http_response_code(405);
      return true;
    }
    return false;
  }
}
