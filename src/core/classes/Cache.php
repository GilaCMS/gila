<?php

class Cache
{
  static $page_name;
  static $uniques;

  static function set ($name, $data, $uniques = null) {
    $dir = Gila::dir(LOG_PATH.'/cacheItem');
    $name = $dir.'/'.str_replace('/', '-', $name);
    $caching_file = $name;
    if($uniques!==null) $caching_file .= '|'.implode('|',$uniques);
    return file_put_contents($caching_file, $data);
  }

  static function get ($name, $time = 3600, $uniques = null) {
    $dir = Gila::dir(LOG_PATH.'/cacheItem');
    if(!is_array($uniques)) {
      $uniques = [$uniques]; 
    }
    $name = $dir.str_replace('/', '-', $name);
    $caching_file = $name;
    if($uniques!==null) $caching_file .= '|'.implode('|',$uniques);

    if(file_exists($caching_file) && filemtime($caching_file)+$time>time()) {
      return file_get_contents($caching_file);
    } else {
      if($uniques !== null) {
        array_map('unlink', glob($name.'*'));
      }
    }
    return null;
  }

  static function remember ($name, $time, $fn, $uniques = null) {
    if($data = self::get($name, $time, $uniques)) {
      return $data;
    }
    if($uniques) {
      $data = $fn($uniques);
    } else {
      $data = $fn();
    }
    self::set($name, $data, $uniques);
    return $data;
  }

  static function page ($name, $time, $uniques = null) {
    if($data = self::get($name, $time, $uniques)) {
      $controller = Router::getController();
      $action = Router::getAction();
      if(isset(Gila::$onaction[$controller][$action])) {
        foreach(Gila::$onaction[$controller][$action] as $fn) $fn();
      }
      echo $data;
      exit;
    }
    ob_start();
    self::$page_name = $name;
    self::$uniques = $uniques;

    register_shutdown_function(function(){
      $out2 = ob_get_contents();
      if(!self::set(self::$page_name, $out2, self::$uniques)) {
        trigger_error("Could not save cache: ".self::$page_name, E_USER_WARNING);
      }
    });
  }
}
