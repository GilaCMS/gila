<?php
use core\models\Page;

class MenuItemTypes
{
  public static $itemTypes;

  public static function defaultData()
  {
    global $db;
    $data = (object) array('type'=>'menu','children' => []);
    $data->children[] = ['type'=>'link','url'=>'','name'=>__('Home')];

    $ql = "SELECT id,title FROM postcategory;";
    $pages = $db->get($ql);
    foreach ($pages as $p) {
      $data->children[] = ['type'=>"postcategory",'id'=>$p[0]];
    }

    foreach (Page::genPublished() as $p) {
      $data->children[] = ['type'=>'page','id'=>$p[0]];
    }

    return (array) $data;
  }

  public static function getItemTypes()
  {
    if (!isset(self::$itemTypes)) {
      self::initItemTypes();
    }
    return self::$itemTypes;
  }

  public static function addItemType($index, $value)
  {
    Gila::addList('menuItemType', $value);
  }

  public static function get($mi)
  {
    if (!isset(self::$itemTypes[$mi['type']])) {
      return false;
    }
    if (!isset(self::$itemTypes[$mi['type']]['response'])) {
      return false;
    }
    return self::$itemTypes[$mi['type']]['response']($mi);
  }

  public static function initItemTypes()
  {
    global $db;
    $pages = Page::genPublished();
    $pageOptions = "";
    foreach ($pages as $p) {
      $pageOptions .= "<option value=\"{$p['id']}\">{$p['title']}</option>";
    }

    $ql = "SELECT id,title FROM postcategory;";
    $cats = $db->get($ql);
    $postcategoryOptions = "";
    foreach ($cats as $p) {
      $postcategoryOptions .= "<option value=\"{$p[0]}\">{$p[1]}</option>";
    }
    // remove widgets untill they are supported
    //$widgetOptions = "";
    //foreach (array_keys(Gila::$widget) as $w) {
    //  $widgetOptions .= "<option value=\"$w\">$w</option>";
    //}

    self::$itemTypes = [
      "link"=>[
        "data"=>[
          "type"=>"link",
          "name"=>"New Link",
          "url"=>"#"
        ],
        "template"=>"<input v-model=\"model.name\" class=\"g-input\" placeholder=\"Name\"><i class=\"fa fa-chevron-right\"></i> <input v-model=\"model.url\" class=\"g-input\" placeholder=\"URI\">"
      ],
      "page"=>[
        "data"=>[
          "type"=>"page",
          "id"=>1
        ],
        "template"=>"<select class=\"g-input\" v-model=\"model.id\">$pageOptions</select>"
      ],
      "postcategory"=>[
        "data"=>[
          "type"=>"postcategory",
          "id"=>1
        ],
        "template"=>"<select class=\"g-input\" v-model=\"model.id\">$postcategoryOptions</select>"
      ],
      /*"widget"=>[
        "data"=>[
          "type"=>"widgets",
          "name"=>"Widget",
          "widget"=>""
        ],
        "template"=>"<input v-model=\"model.name\" class=\"g-input\" placeholder=\"Name\"><select class=\"g-input\" v-model=\"model.widget\">$widgetOptions</select>",
      ],*/
      "dir"=>[
        "data"=>[
          "type"=>"dir",
          "name"=>"New Directory",
          "children"=>[]
        ],
        "template"=>"<input v-model=\"model.name\" class=\"g-input\" placeholder=\"Name\">",
      ]
    ];
    $custom = Gila::getList('menuItemType');
    foreach ($custom as $n) {
      self::$itemTypes[$n[0]] = $n[1];
    }
  }
}
