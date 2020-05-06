<?php
namespace core\models;
use core\models\Page;

class Menu
{
  static function defaultData()
  {
    global $db;
    $widget_data = (object) array('type'=>'menu','children' => []);
    $widget_data->children[] = ['type'=>'link','url'=>'','name'=>__('Home')];
    $ql = "SELECT id,title FROM postcategory;";
    $pages = $db->get($ql);
    foreach ($pages as $p) {
      $widget_data->children[] = ['type'=>"postcategory",'id'=>$p[0]];
    }
    foreach (Page::genPublished() as $p) {
      $widget_data->children[] = ['type'=>'page','id'=>$p[0]];
    }
    return (array) $widget_data;
  }
}
