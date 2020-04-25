<?php
namespace core\models;

class widget
{
  static function getById($id)
  {
    $db = \Gila::slaveDB();
    $res = $db->query("SELECT * FROM widget WHERE id=?",$id);
    return mysqli_fetch_object($res);
  }

  static function getByWidget($w)
  {
    $db = \Gila::slaveDB();
    return $db->query("SELECT * FROM widget WHERE widget=?",$w);
  }

  static function getActiveByArea($area)
  {
    $db = \Gila::slaveDB();
    $db->connect();
    return $db->get("SELECT * FROM widget WHERE active=1 AND area=? ORDER BY pos;",$area);
  }

  static function update($data)
  {
    global $db;
    $widget = self::getById($data['widget_id']);
    $widget_folder = 'src/'.\Gila::$widget[$widget->widget];
    $fields = include $widget_folder.'/widget.php';

    foreach($data['option'] as $key=>$value) {
      $allowed = $fields[$key]['allow-tags'] ?? false;
      if($allowed==false) {
        if(!json_decode($data['option'][$key])) {
          $data['option'][$key] = strip_tags($data['option'][$key]);
        }
      } else if($allowed!==true) {
        $data['option'][$key] = strip_tags($data['option'][$key], $allowed);
      }
    }
    $widget_data = isset($data['option']) ? json_encode($data['option']) : '[]';

    $db->query("UPDATE widget SET data=?,area=?,pos=?,title=?,active=? WHERE id=?",
      [$widget_data, $data['widget_area'], $data['widget_pos'],
      strip_tags($data['widget_title']), $data['widget_active'], $data['widget_id']]);

    $r = $db->get("SELECT * FROM widget WHERE id=?",[$data['widget_id']])[0];
    return json_encode(['fields'=>['id','title','widget','area','pos','active'],
      'rows'=>[[$r['id'],$r['title'],$r['widget'],$r['area'],$r['pos'],$r['active']]],
      'totalRows'=>1]);
  }
}
