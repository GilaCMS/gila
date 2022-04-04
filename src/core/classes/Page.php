<?php
namespace Gila;

class Page
{
  public static function getById($id)
  {
    $res = DB::query("SELECT id,title,description,updated,`language`,publish,slug,template FROM `page` WHERE id=?;", [$id]);
    if ($res) {
      return $r = mysqli_fetch_array($res);
    }
    return null;
  }


  public static function getByIdSlug($id, $published=true)
  {
    $publish = $published? 'publish=1 AND': '';
    $query = 'SELECT id,title,description,updated,`language`,publish,slug,template FROM `page`';

    $res = DB::query(
      "$query WHERE $publish (id=? OR (slug=? AND `language`=?))",
      [$id, $id, Config::lang()]
    );

    if ($row = mysqli_fetch_array($res)) {
      if ($blocks = DB::value("SELECT blocks FROM `page` WHERE id=?;", [$row['id']])) {
        $blocks = json_decode($blocks);
        $row['page'] = View::blocks($blocks, 'page_'.$row['id']);
      }
      return $row;
    } else {
      $res = DB::query(
        "$query WHERE $publish (id=? OR slug=?)",
        [$id, $id]
      );
      if ($row = mysqli_fetch_array($res)) {
        if ($blocks = DB::value("SELECT blocks FROM `page` WHERE id=?;", [$row['id']])) {
          $blocks = json_decode($blocks);
          $row['page'] = View::blocks($blocks, 'page_'.$row['id']);
        }
        return $row;
      }
    }

    if (empty($id) && $published) {
      $res = DB::query(
        "$query WHERE publish=1 AND `language`=?
        UNION $query WHERE publish=1;",
        [Config::lang()]
      );
      if ($row = mysqli_fetch_array($res)) {
        if ($blocks = DB::value("SELECT blocks FROM `page` WHERE id=?;", [$row['id']])) {
          $blocks = json_decode($blocks);
          $row['page'] = View::blocks($blocks, 'page_'.$row['id']);
        }
        return $row;
      }
    }

    return null;
  }

  public static function getBySlug($id)
  {
    $res = DB::query("SELECT id,title,updated,publish,slug,`language`
    FROM `page` WHERE publish=1 AND slug=?;", [$id]);
    if ($res) {
      return mysqli_fetch_array($res);
    }
    return false;
  }

  public static function genPublished()
  {
    $ql = "SELECT id,title,slug,`language` FROM `page` WHERE publish=1;";
    $res = DB::query($ql);
    while ($r = mysqli_fetch_array($res)) {
      yield $r;
    }
    return;
  }

  public static function redirect($id)
  {
    $to = DB::value("SELECT `to_slug` FROM redirect WHERE active=1 AND `from_slug`=?;", [$id]);
    return $to;
  }

  public static function inCachedList($id)
  {
    $array = Cache::remember('page_cache_list', 86400, function ($u) {
      $ql = "SELECT slug FROM `page` WHERE publish=1";
      return json_encode(DB::getList($ql));
    }, [Config::mt('page')]);
    return (in_array($id, json_decode($array, true)));
  }
}
