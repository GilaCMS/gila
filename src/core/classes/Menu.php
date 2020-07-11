<?php
use core\models\Page;

class Menu
{
  public static function defaultData()
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

  public static function getHtml($items, $base="")
  {
    $html = "";
    foreach ($items as $key => $item) {
      if (isset($item['access'])) {
        if (!Session::hasPrivilege($item['access'])) {
          continue;
        }
      }
      if (isset($item['icon'])) {
        $icon = 'fa-'.$item['icon'];
      } else {
        $icon='';
      }
      $url = $item[1]=='#'? 'javascript:void(0)': htmlentities($item[1]);
      $badge = "";
      if (isset($item['counter'])) {
        $c = is_callable($item['counter'])? $item['counter'](): $item['counter'];
        if ($c>0) {
          $badge = " <span class=\"g-badge\">$c</span>";
        }
      }
      $liClass = isset($item['children'])? ' class="dropdown"': '';
      $html .= "<li$liClass><a href='".$url."'><i class='fa {$icon}'></i>";
      $html .= " <span>".__("$item[0]")."</span>$badge</a>";
      if (isset($item['children'])) {
        $html .= "<ul class=\"dropdown-menu\">";
        $html .= Menu::getHtml($item['children'], $base);
        $html .= "</ul>";
        $html .= "</ul>";
      }
      $html .="</li>";
    }
    return $html;
  }
}
