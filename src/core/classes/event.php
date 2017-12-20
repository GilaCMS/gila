<?php

class event {

  static private $handlers = [];

  static function listen($event, $handler)
  {
    if (!isset(event::$handlers[$event])) event::$handlers[$event] = [];
    event::$handlers[$event][] = $handler;
  }

  static function fire($event, $params = null)
  {
    if (isset(event::$handlers[$event])) foreach (event::$handlers[$event] as $handler) {
        if ($params == null) $handler(); else $handler($params);
    }
  }

  static function get($event, $default, $params = null)
  {
      if (isset(event::$handlers[$event])) {
          if ($params == null)
            return event::$handlers[$event][0]();
          else $handler($params);
            return event::$handlers[$event][0]($params);
      }
      return $default;
  }

}
