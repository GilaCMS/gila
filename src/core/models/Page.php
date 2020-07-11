<?php
namespace core\models;

class Page
{
  public static function getById($id)
  {
    global $db;
    $res = $db->query("SELECT id,title,content as `page`,updated,publish,slug,template FROM `page` WHERE id=?;", [$id]);
    if ($res) {
      return $r = mysqli_fetch_array($res);
    }
    return false;
  }


  public static function getByIdSlug($id)
  {
    global $db;
    $res = $db->query("SELECT id,title,content as `page`,updated,publish,slug,template FROM `page` WHERE publish=1 AND (id=? OR slug=?);", [$id,$id]);
    if ($row = mysqli_fetch_array($res)) {
      if ($blocks = $db->value("SELECT blocks FROM `page` WHERE id=?;", [$row['id']])) {
        $blocks = json_decode($blocks);
        $row['page'] .= \View::blocks($blocks);
      }
      return $row;
    }
    return false;
  }

  public static function getBySlug($id)
  {
    global $db;
    $res = $db->query("SELECT id,title,content as `page`,updated,publish,slug FROM `page` WHERE publish=1 AND slug=?;", [$id]);
    if ($res) {
      return mysqli_fetch_array($res);
    }
    return false;
  }

  public static function genPublished()
  {
    global $db;
    $ql = "SELECT id,title,slug FROM `page` WHERE publish=1;";
    $res = $db->query($ql);
    while ($r = mysqli_fetch_array($res)) {
      yield $r;
    }
    return;
  }
}
