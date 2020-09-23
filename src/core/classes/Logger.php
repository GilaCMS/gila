<?php

namespace Gila;

class Logger
{
  private static $savedStat = false;
  private $handlers = [];
  private $file = null;

  /**
   * If filepath is set logs will be printed on it
   */
  public function __construct($filepath = null)
  {
    if ($filepath != null) {
      $this->file = $filepath;
    }
  }

  /**
   * System is unusable.
   */
  public function emergency($message, array $context = [])
  {
    $this->log('emergency', $message, $context);
  }

  /**
   * Action must be taken immediately.
   */
  public function alert($message, array $context = [])
  {
    $this->log('alert', $message, $context);
  }
  /**
   * Critical conditions.
   */
  public function critical($message, array $context = [])
  {
    $this->log('critical', $message, $context);
  }

  /**
   * Runtime errors that do not require immediate action but should typically
   * be logged and monitored.
   */
  public function error($message, array $context = [])
  {
    $this->log('error', $message, $context);
  }

  /**
   * Exceptional occurrences that are not errors.
   *
   * Example: Use of deprecated APIs, poor use of an API, undesirable things
   * that are not necessarily wrong.
   */
  public function warning($message, array $context = [])
  {
    $this->log('warning', $message, $context);
  }

  /**
   * Normal but significant events.
   */
  public function notice($message, array $context = [])
  {
    $this->log('notice', $message, $context);
  }

  /**
   * Interesting events.
   *
   * Example: User logs in, SQL logs.
   */
  public function info($message, array $context = [])
  {
    $this->log('info', $message, $context);
  }

  /**
   * Detailed debug information.
   */
  public function debug($message, array $context = [])
  {
    $this->log('debug', $message, $context);
  }

  /**
   * Logs with an arbitrary level.
   */
  public function log($level, $message, array $context = [])
  {
    if ($this->file != null) {
      $line = date("Y-m-d H:i:s").','.$level.','.$message.','.json_encode($context, true)."\n";
      @file_put_contents($this->file, $line, FILE_APPEND);
    }

    foreach ($this->handlers as $handler) {
      $handler->handle($level, $message, $context = []);
    }
  }

  /**
   * Adds a new handler to the logger.
   */
  public function pushHandler(LogHandler $handler)
  {
    array_unshift($this->handlers, $handler);
  }

  public static function stat($type = 'web', $value = null, array $context = [])
  {
    if ($value===null) {
      if (self::$savedStat) {
        return;
      }
      self::$savedStat = true;
    }
    //if(UserAgent::isBot($_SERVER['HTTP_USER_AGENT'])===true) return;
    $stat_log = new Logger(LOG_PATH.'/stats/'.date("Y-m-d").'.'.$type.'.log');
    $stat_log->log($value ?? Router::$url, $_SERVER['HTTP_USER_AGENT']??'', $context);
  }

  public static function getStat($type = 'web', $date = null)
  {
    $result = [];
    if ($date===null) {
      $date = date("Y-m-d");
    }
    if (!is_array($date)) {
      $date = [$date, $date];
    }

    $begin = new \DateTime($date[0]);
    $end = new \DateTime($date[1]);
    $end = $end->modify('+1 day');

    $interval = new \DateInterval('P1D');
    $daterange = new \DatePeriod($begin, $interval, $end);

    foreach ($daterange as $date) {
      $filePath = LOG_PATH.'/stats/'.$date->format("Y-m-d").'.'.$type.'.log';
      if (($handle = fopen($filePath, "r")) !== false) {
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
          $result[] = $data;
        }
        fclose($handle);
      }
    }

    return $result;
  }
}
