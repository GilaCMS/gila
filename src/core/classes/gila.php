<?php

//  common functions will be put here

class gila {
  
  
  static function controllers($key, $list)
  {
    if (!is_array($list)) $list[0]=$list;
    foreach ($list as $k=>$item) {
      $GLOBALS[$key][$k]=$item;
    }
  }
  
  static function widgets($key, $list)
  {
    if (!is_array($list)) $list[0]=$list;
    foreach ($list as $k=>$item) {
      $GLOBALS[$key][$k]=$item;
    }
  }

  static function post($id)
  {

  }

  static function menu($id)
  {
    
  }

}
