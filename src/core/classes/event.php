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

}
