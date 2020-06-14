<?php
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

  static function getHtml($items, $base="")
  {
    $html = "";
    foreach ($items as $key => $item) {
      if(isset($item['access'])) if(!Gila::hasPrivilege($item['access'])) continue;
      if(isset($item['icon'])) $icon = 'fa-'.$item['icon']; else $icon='';
      $url = $item[1]=='#'? Gila::url($base.'#'): $item[1];
      $badge = "";
      if(isset($item['counter'])) {
        $c = is_callable($item['counter'])? $item['counter'](): $item['counter'];
        if($c>0) $badge = " <span class=\"g-badge\">$c</span>";
      }
      $html .= "<li><a href='".$url."'><i class='fa {$icon}'></i>";
      $html .= " <span>".__("$item[0]")."</span>$badge</a>";
      if(isset($item['children'])) {
        $html .= "<ul class=\"dropdown\">";
        $html .= Menu::getHtml($item['children'], $base);
        $html .= "</ul>";
      }
      $html .="</li>";
    }
    return $html;
  }
}
