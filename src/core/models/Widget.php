<?php
namespace Gila;

class widget
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
    $widget_folder = 'src/'.Config::$widget[$widget->widget];
    $fields = include $widget_folder.'/widget.php';

    foreach ($data['option'] as $key=>$value) {
      $allowed = $fields[$key]['allow_tags'] ?? false;
      $data['option'][$key] = utf8_decode($data['option'][$key]);
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
}
