<?php

namespace Gila;

class Router
{
  private static $args = [];
  public static $url;
  public static $caching = false;
  public static $caching_file;
  public static $controllers = [];
  public static $on_controller = [];
  public static $actions = [];
  public static $before = [];
  public static $onaction = [];
  public static $method;
  public static $controller;
  public static $action;
  private static $route = [];

  public function __construct()
  {
    self::run($_GET['p'] ?? ($_GET['url'] ?? false));
  }

  public static function run($_p = false)
  {
    global $c;

    self::$method = $_SERVER['REQUEST_METHOD'];
    self::setPath($_p);
    if (self::matchRoutes(self::$route)==true) {
      return;
    }

    $controller = self::getController();
    $ctrlPath = self::$controllers[$controller];
    $ctrlClass = substr($ctrlPath, strrpos($ctrlPath, '/')+1);
    require_once 'src/'.$ctrlPath.'.php';
    $action = self::getAction($ctrlClass);

    if ($action === '') {
      @http_response_code(404);
      return;
    }

    $c = new $ctrlClass();

    // find function to run after controller construction
    if (isset(self::$on_controller[$controller])) {
      foreach (self::$on_controller[$controller] as $fn) {
        $fn();
      }
    }

    $action_fn = $action.'Action';
    $action_m = $action_fn.ucwords(self::$method);

    if (isset(Router::$before[$controller][$action])) {
      foreach (Router::$before[$controller][$action] as $fn) {
        $fn();
      }
    }
    if (isset(self::$actions[$controller][$action])) {
      @call_user_func_array(self::$actions[$controller][$action], self::$args);
    } elseif (method_exists($c, $action_m)) {
      @call_user_func_array([$c, $action_m], self::$args);
    } elseif (method_exists($c, $action_fn)) {
      @call_user_func_array([$c, $action_fn], self::$args);
    } else {
      @http_response_code(404);
    }
  }

  public static function getController():string
  {
    if (isset(self::$controller)) {
      return self::$controller;
    }
    $default = Config::get('default-controller')??'admin';
    self::$controller = self::request('c', $default);

    if (isset(self::$args[0]) && isset(self::$controllers[self::$args[0]])) {
      self::$controller = self::$args[0];
      array_shift(self::$args);
    }
    if (!isset(self::$controllers[self::$controller])) {
      self::$controller = 'admin';
    }
    return self::$controller;
  }

  public static function getAction($ctrClass = ''):string
  {
    if (isset(self::$action)) {
      return self::$action;
    }
    $args = &self::$args;
    $action = $args[0]??'index';

    if (!method_exists($ctrClass, $action.'Action') &&
        !isset(self::$actions[self::getController()][$action])) {
      if (method_exists($ctrClass, 'indexAction')) {
        $action = $args[0] ? 'index' : 'index';
      } else {
        $action = '';
      }
    }

    if (isset($args[0]) && $args[0]===$action) {
      array_shift($args);
    }
    
    $action = explode('.', $action);
    self::$action = $action[0];
    return self::$action;
  }

  public static function add($string, $fn, $method = 'GET', $permission = null)
  {
    self::$route[] = [$string, $fn, $method, $permission];
  }

  public static function get($string, $fn, $permission = null)
  {
    self::add($string, $fn, 'GET', $permission);
  }

  public static function post($string, $fn, $permission = null)
  {
    self::add($string, $fn, 'POST', $permission);
  }

  /**
  * Returns a get parameter value
  * @param $key (string) Parameter's name
  * @param $n optional (int) Parameter's expected position in a pretty url.
  * @return Value or null if paremeter is not found.
  */
  public static function param($key, $n = null)
  {
    if ($n!==null && isset(self::$args[$n-1]) && self::$args[$n-1]!==null) {
      return self::$args[$n-1];
    } elseif (isset($_GET[$key])) {
      return $_GET[$key];
    } elseif (isset($_GET['var'.$n])) {
      return $_GET['var'.$n];
    } else {
      return null;
    }
  }

  public static function request($key, $default=null)
  {
    $r = $_REQUEST[$key] ?? $default;
    return @strip_tags($r);
  }

  public static function path()
  {
    return self::getPath();
  }

  /**
  * Registers a new controller
  * $c:string Controllers name
  * $file:string Controller’s filepath without the php extension
  * $name:string Optional. Controller’s class name, $c is used by default
  */
  public static function controller($name, $path)
  {
    self::$controllers[$name] = $path;
  }

  /**
  * Registers a new action
  */
  public static function action($c, $action, $fn)
  {
    self::$actions[$c][$action] = $fn;
  }

  public static function before($c, $action, $fn)
  {
    self::$before[$c][$action][] = $fn;
  }

  public static function onAction($c, $action, $fn)
  {
    self::$onaction[$c][$action][] = $fn;
  }

  public static function onController($c, $fn)
  {
    self::$on_controller[$c][] = $fn;
  }

  public static function setPath($_p)
  {
    if ($_p!==false) {
      if (substr($_p, -1)=='/') {
        $_p = substr($_p, 0, -1);
      }
      self::$url = strip_tags($_p);
      self::$args = explode("/", self::$url);
      if (isset(self::$args[0])) {
        if (self::$args[0]===Config::lang() ||
        (Config::get('languages') && in_array(self::$args[0], Config::get('languages')))) {
          Config::lang(self::$args[0]);
          self::$url = substr(self::$url, 3);
          array_shift(self::$args);
        }
      }
    } else {
      self::$url = false;
      self::$args = [];
    }
  }
  public static function getPath()
  {
    return self::$url??'';
  }
  public static function setUrl($_p)
  {
    self::setPath($_p);
  }

  public static function matchRoutes(&$routes)
  {
    $matched = false;
    foreach ($routes as $route) {
      if (preg_match('#^'.$route[0].'$#', self::$url, $matches)) {
        $matched = true;
        if (self::$method == $route[2]) {
          if ($route[3]!==null && Session::hasPrivilege($route[3])===false) {
            @http_response_code(403);
          } else {
            array_shift($matches);
            @call_user_func_array($route[1], $matches);
          }
          return true;
        } elseif (self::$method == 'OPTIONS') {
          @http_response_code(204);
          return true;
        }
      }
    }
    if ($matched) {
      @http_response_code(405);
      return true;
    }
    return false;
  }

  public static function oki()
  {
    echo 'oook';
  }
}
