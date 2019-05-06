<?php
namespace core\models;

class page
{
  static function getById($id)
  {
    global $db;
    $res = $db->query("SELECT id,title,content as `page`,updated,publish,slug FROM `page` WHERE id=?;",[$id]);
    if($res) return $r = mysqli_fetch_array($res);
    return false;
  }


  static function getByIdSlug($id)
  {
    global $db;
    $res = $db->query("SELECT id,title,content as `page`,updated,publish,slug FROM `page` WHERE publish=1 AND (id=? OR slug=?);",[$id,$id]);
    if($row = mysqli_fetch_array($res)) {
      if($blocks = $db->value("SELECT blocks FROM `page` WHERE id=?;", [$row['id']])) {
        $blocks = json_decode($blocks);
        ob_start();
        foreach($blocks as $b) {
          \view::widget_body($b->_type, $b);
        }
        $out = ob_get_contents();
        ob_end_clean();
        $row['page'] .= $out;
      }
      return $row;
    }
    return false;
  }

  static function getBySlug($id)
  {
    global $db;
    $res = $db->query("SELECT id,title,content as `page`,updated,publish,slug FROM `page` WHERE publish=1 AND slug=?;",[$id]);
    if($res) return mysqli_fetch_array($res);
    return false;
  }

  static function genPublished()
  {
    global $db;
    $ql = "SELECT id,title,slug FROM `page` WHERE publish=1;";
    $res = $db->query($ql);
    while($r = mysqli_fetch_array($res)) {
      yield $r;
    }
    return;
  }

}
