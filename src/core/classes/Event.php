<?php

/**
* Registers and fires events (hooks)
*/
class Event
{
  static private $handlers = [];

  /**
  * Sets a new function to run when an event is triggered later
  * @param $event (string) The event name
  * @param $handler (function) The function to call
  */
  static function listen($event, $handler)
  {
    if (!isset(Event::$handlers[$event])) Event::$handlers[$event] = [];
    Event::$handlers[$event][] = $handler;
  }

  /**
  * Fires an event and calls all handling functions.
  * @param $event (string) – The event name.
  * @param $params (optional) Parameters to send to handlers.
  */
  static function fire($event, $params = null)
  {
    $response = false;
    if (isset(Event::$handlers[$event])) foreach (Event::$handlers[$event] as $handler) {
      if ($params == null) $handler(); else $handler($params);
      $response = true;
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
  static function get($event, $default, $params = null)
  {
    if (isset(Event::$handlers[$event])) {
      if ($params == null)
        return Event::$handlers[$event][0]();
      else
        return Event::$handlers[$event][0]($params);
    }
    return $default;
  }
}
