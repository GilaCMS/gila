<?php

namespace Gila;

class Log
{
  private static $savedStat = false;
  private $handlers = [];
  private $file = null;
  private $starttime;

  public static function error($error)
  {
    error_log($error);
  }

  public static function time($point = '')
  {
    if (!isset(self::$starttime)) {
      $starttime = microtime(true);
    }

    $end = microtime(true);
    $log = new Logger(LOG_PATH.'/timeDebug.log');
    $log->log(round($end-self::$starttime, 6), $point, ['uri'=>$_GET['p']??'']);
    self::$starttime = $end;  
  }

  public static function debug($message, array $context = [])
  {
    $log = new Logger(LOG_PATH.'/debug.log');
    $log->debug($message, $context);
  }

}

