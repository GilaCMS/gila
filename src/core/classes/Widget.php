<?php
namespace Gila;

class Widget
{
  public static function getById($id)
  {
    $res = DB::query("SELECT * FROM widget WHERE id=?", $id);
    return mysqli_fetch_object($res);
  }

  public static function getByWidget($w)
  {
    return DB::query("SELECT * FROM widget WHERE widget=?", $w);
  }

  public static function getActiveByArea($area)
  {
    DB::connect();
    return DB::get(
      "SELECT * FROM widget WHERE active=1 AND area=?
    AND (`language` IS NULL OR language='' OR language=?) ORDER BY pos;",
      [$area, Config::lang()]
    );
  }

  public static function update($data)
  {
    $widget = self::getById($data['widget_id']);
    $fields = self::getFields($widget->widget);

    foreach ($data['option'] as $key=>$value) {
      $allowed = $fields[$key]['allow_tags'] ?? false;
      $purify = $fields[$key]['purify'] ?? true;
      if ($purify===true) {
        $data['option'][$key] = HtmlInput::purify($data['option'][$key], $allowed);
      }
    }
    $widget_data = isset($data['option']) ? json_encode($data['option']) : '[]';
    $title = HtmlInput::purify($data['widget_title']);

    DB::query(
      "UPDATE widget SET data=?,area=?,pos=?,title=?,active=?,`language`=? WHERE id=?",
      [$widget_data, $data['widget_area'], $data['widget_pos']??0, $title,
      $data['widget_active']??0, $data['widget_language']??'NULL', $data['widget_id']]
    );

    $r = DB::get("SELECT * FROM widget WHERE id=?", [$data['widget_id']])[0];
    return json_encode(['fields'=>['id','title','widget','area','pos','language','active'],
      'rows'=>[[$r['id'],$r['title'],$r['widget'],$r['area'],$r['pos'],$r['language'],$r['active']]],
      'totalRows'=>1]);
  }

  public static function getWidgetFile($widget)
  {
    if (!isset(Config::$widget[$widget])) {
      $widget = explode('--', $widget)[0];
    }
    return 'src/'.Config::$widget[$widget].'/widget.php';
  }

  public static function getFields($widget)
  {
    $widgetData = include self::getWidgetFile($widget);
    return $widgetData['fields'] ?? $widgetData;
  }

  public static function getKeys($widget)
  {
    $widgetData = include self::getWidgetFile($widget);
    return $widgetData['keys'] ?? "";
  }

  public static function getData($widget)
  {
    $widgetData = include self::getWidgetFile($widget);
    return $widgetData ?? [];
  }

  public static function getList($term=null)
  {
    $primary = [];
    $secondary = [];
    foreach (Config::$widget as $widget=>$value) {
      $keys = self::getKeys($widget);
      if ($keys==='removed') {
        continue;
      }
      if (in_array($term, explode(',', $keys))) {
        $primary[$widget] = $value;
      } elseif ($term===null || $keys==="") {
        $secondary[$widget] = $value;
      }
    }
    return array_merge($primary, $secondary);
  }
}
