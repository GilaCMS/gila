<?php

//  common functions will be put here

class gila {

    function __construct()
    {

    }

  static function controllers($list)
  {
    if (!is_array($list)) $list[0]=$list;
    foreach ($list as $k=>$item) {
      $GLOBALS['config']['controller'][$k]=$item;
    }
  }

  static function widgets($list)
  {
    if (!is_array($list)) $list[0]=$list;
    foreach ($list as $k=>$item) {
      $GLOBALS['config']['widget'][$k]=$item;
    }
  }

  static function post($id)
  {

  }

  static function config($key, $value = null)
  {
      if ($value == null) {
          return $GLOBALS['config'][$key];
      }
      else {
          $GLOBALS['config'][$key] = $value;
      }
  }

  static function menu($id = null)
  {
      global $db;

      $data = json_decode( $db->value("SELECT data FROM widget WHERE widget='menu' LIMIT 1"),true);
      foreach ($data as $k=>$d) {
          if (isset($d['children'])) {
              if (is_array($d['children'])) $data[$k]['children'] = $d['children'][0];
              if (count($data[$k]['children'])==0) unset($data[$k]['children']);
          }
      }
      //echo "<pre>".var_export($data,true)."</pre>";
      /*$data = [
          ['title'=>'Technology','url'=>'tech'],
          ['title'=>'Mobile','url'=>'#','children'=>[
              ['title'=>'Android','url'=>'#'],['title'=>'Samsung','url'=>'#'],['title'=>'Nokia','url'=>'#']
              ]],
          ['title'=>'Laptops','url'=>'latops'],
          ['title'=>'Tablets','url'=>'tablets'],
          ['title'=>'Contact Us','url'=>'pages/contact.html']
      ];*/

      if ($id == null) return $data;

  }

}
