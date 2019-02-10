<?php

class cache
{
  static function set ($name, $data, $uniques = null) {
    $dir = gila::dir('log/cacheItem/');
    $caching_file = $dir.$name.'|'.implode('|',$uniques);
    // save asycronimously?
    return file_put_contents($caching_file, $data);
  }

  static function get ($name, $time = 3600, $uniques = null) {
    $dir = gila::dir('log/cacheItem/');
    if(!is_array($uniques)) {
        $uniques[] = $uniques; 
    }
    $caching_file = $dir.$name.'|'.implode('|', $uniques);

    if(file_exists($caching_file) && filemtime($caching_file)+$time>time()) {
        $data = file_get_contents($caching_file);
        if(substr($caching_file, -4)=='json') $data = json_decode($data, true);
        return $data;
    } else {
        if(count($uniques)>1) {
            array_map('unlink', glob($uniques[0].'*'));
        }
    }
    return null;
  }

  static function remember ($name, $time, $fn, $uniques = null) {
    if($data = self::get($name, $time, $uniques)) {
        return $data;
    }
    $data = $fn();
    self::set($name, $data, $uniques);
    return $data;
  }
}
