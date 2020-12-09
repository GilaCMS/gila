<?php

namespace Gila;

class Cache
{
  public static $page_name;
  public static $uniques;
  public static $cachePath = LOG_PATH.'/cacheItem/';

  public static function set($name, $data, $uniques = [])
  {
    $name = self::$cachePath.str_replace('/', '-', $name);
    $caching_file = $name.'|'.implode('|', $uniques);
    return file_put_contents($caching_file, $data);
  }

  public static function get($name, $time = 3600, $uniques = [])
  {
    if (!is_array($uniques)) {
      $uniques = [$uniques];
    }
    $name = self::$cachePath.str_replace('/', '-', $name);
    $caching_file = $name.'|'.implode('|', $uniques);

    if (file_exists($caching_file) && filemtime($caching_file)+$time>time()) {
      return file_get_contents($caching_file);
    } else {
      if ($uniques !== null) {
        array_map('unlink', glob($name.'*'));
      }
    }
    return null;
  }

  public static function remember($name, $time, $fn, $uniques = [])
  {
    if ($data = self::get($name, $time, $uniques)) {
      return $data;
    }
    if ($uniques===[]) {
      $data = $fn();
    } else {
      $data = $fn($uniques);
    }
    self::set($name, $data, $uniques);
    return $data;
  }

  public static function page($name, $time, $uniques = null)
  {
    if ($_SERVER['REQUEST_METHOD']!=="GET") {
      return;
    }
    if ($data = self::get($name, $time, $uniques)) {
      $controller = Router::getController();
      $action = Router::getAction();
      if (isset(Config::$onaction[$controller][$action])) {
        foreach (Config::$onaction[$controller][$action] as $fn) {
          $fn();
        }
      }
      echo $data;
      exit;
    }
    ob_start();
    self::$page_name = $name;
    self::$uniques = $uniques;

    register_shutdown_function(function () {
      $out2 = ob_get_contents();
      if (!self::set(self::$page_name, $out2, self::$uniques)) {
        trigger_error("Could not save cache: ".self::$page_name, E_USER_WARNING);
      }
    });
  }
}
