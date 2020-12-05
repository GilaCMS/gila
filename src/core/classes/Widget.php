<?php
namespace Gila;

class Widget
{
  public static function getById($id)
  {
    global $db;
    $res = $db->query("SELECT * FROM widget WHERE id=?", $id);
    return mysqli_fetch_object($res);
  }

  public static function getByWidget($w)
  {
    global $db;
    return $db->query("SELECT * FROM widget WHERE widget=?", $w);
  }

  public static function getActiveByArea($area)
  {
    global $db;
    $db->connect();
    return $db->get("SELECT * FROM widget WHERE active=1 AND area=? ORDER BY pos;", $area);
  }

  public static function update($data)
  {
    global $db;
    $widget = self::getById($data['widget_id']);
    $fields = self::getFields($widget->widget);

    foreach ($data['option'] as $key=>$value) {
      $allowed = $fields[$key]['allow_tags'] ?? false;
      $data['option'][$key] = HtmlInput::purify($data['option'][$key], $allowed);
    }
    $widget_data = isset($data['option']) ? json_encode($data['option']) : '[]';
    $title = HtmlInput::purify($data['widget_title']);

    $db->query(
      "UPDATE widget SET data=?,area=?,pos=?,title=?,active=? WHERE id=?",
      [$widget_data, $data['widget_area'], $data['widget_pos']??0,
      $title, $data['widget_active']??0, $data['widget_id']]
    );

    $r = $db->get("SELECT * FROM widget WHERE id=?", [$data['widget_id']])[0];
    return json_encode(['fields'=>['id','title','widget','area','pos','active'],
      'rows'=>[[$r['id'],$r['title'],$r['widget'],$r['area'],$r['pos'],$r['active']]],
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
