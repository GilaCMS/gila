<?php

class Cache
{
  static $name;
  static $uniques;

  static function set ($name, $data, $uniques = null) {
    $dir = Gila::dir(LOG_PATH.'/cacheItem/');
    $caching_file = $name;
    if($uniques) $caching_file .= '|'.implode('|',$uniques);
    $caching_file = $dir.str_replace('/', '_', $caching_file);
    return file_put_contents($caching_file, $data);
  }

  static function get ($name, $time = 3600, $uniques = null) {
    $dir = Gila::dir(LOG_PATH.'/cacheItem/');
    if(!is_array($uniques)) {
        $uniques[] = $uniques; 
    }
    $caching_file = $name;
    if($uniques) $caching_file .= '|'.implode('|',$uniques);
    $caching_file = $dir.str_replace('/', '_', $caching_file);

    if(file_exists($caching_file) && filemtime($caching_file)+$time>time()) {
      return file_get_contents($caching_file);
    } else {
      if($uniques !== null) {
        array_map('unlink', glob($uniques[0].'*'));
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
      echo $data;
      exit;
    }
    ob_start();
    self::$name = $name;
    self::$uniques = $uniques;

    register_shutdown_function(function(){
      $out2 = ob_get_contents();
      if(!self::set(self::$name, $out2, self::$uniques)) {
        trigger_error("Could not save cache: ".self::$caching_file);
      }
    });
  }
}
