<?php

/**
* Registers and fires events (hooks)
*/
namespace Gila;

class Event
{
  private static $handlers = [];

  /**
  * Sets a new function to run when an event is triggered later
  * @param $event (string) The event name
  * @param $handler (function) The function to call
  */
  public static function listen($event, $handler)
  {
    if (!isset(self::$handlers[$event])) {
      self::$handlers[$event] = [];
    }
    self::$handlers[$event][] = $handler;
  }

  /**
  * Fires an event and calls all handling functions.
  * @param $event (string) – The event name.
  * @param $params (optional) Parameters to send to handlers.
  */
  public static function fire($event, $params = null)
  {
    $response = false;
    if (isset(self::$handlers[$event])) {
      foreach (self::$handlers[$event] as $handler) {
        if ($params === null) {
          $handler();
        } else {
          $handler($params);
        }
        $response = true;
      }
    }
    return $response;
  }

  /**
  * Fires the only handler (if registered) of an event and expects a return value
  * @param $event (string) The event name
  * @param $default The value to return if there was no function called
  * @param $params (optional) Parameters to send to the function
  * @return The result of the registered handler
  * @see fire()
  */
  public static function get($event, $default, $params = null)
  {
    if (isset(self::$handlers[$event])) {
      if ($params === null) {
        return self::$handlers[$event][0]();
      } else {
        return self::$handlers[$event][0]($params);
      }
    }
    return $default;
  }

  public static function log($type, $data=[])
  {
    DB::query("INSERT INTO event_log(`type`,user_id,`data`) VALUES(?,?,?)", [
      $type, Session::userId(), json_encode($data)
    ]);
  }
}
