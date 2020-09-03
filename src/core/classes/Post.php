<?php
namespace Gila;

class Post
{
  public static function getById($id)
  {
    return self::getByIdSlug($id);
  }

  public static function getByIdSlug($id)
  {
    global $db;
    $ql="SELECT id,description,title,post,publish,slug,updated,user_id,
      (SELECT a.value FROM postmeta a WHERE a.post_id=post.id AND vartype='thumbnail') as img,
      (SELECT GROUP_CONCAT(b.value SEPARATOR ',') FROM postmeta b WHERE b.post_id=post.id AND vartype='tag') as tags
      FROM post WHERE (id=? OR slug=?)";
    $res = $db->read()->query($ql, [$id,$id]);
    if ($row = mysqli_fetch_array($res)) {
      if ($blocks = $db->read()->value("SELECT blocks FROM post WHERE (id=? OR slug=?);", [$id,$id])) {
        $blocks = json_decode($blocks);
        $row['post'] .= View::blocks($blocks, 'post'.$row['id']);
      }
      $row['url'] = Config::make_url('blog', '', ['p'=>$row['id'],'slug'=>$row['slug']]);
      return $row;
    }
    return false;
  }

  public static function meta($id, $meta, $value = null)
  {
    global $db;
    if ($value==null) {
      $ql = "SELECT value FROM postmeta where post_id=? and vartype=?;";
      return $db->getList($ql, [$id, $meta]);
    }
    $ql = "INSERT INTO postmeta(post_id,vartype,value) VALUES('?','?','?');";
    return $db->query($ql, [$id, $meta, $value]);
  }

  public static function getMeta($meta)
  {
    global $db;
    $ql = "SELECT value,COUNT(*) AS count FROM postmeta where vartype=? GROUP BY value;";
    return $db->read()->get($ql, [$meta]);
  }

  public static function getByUserID($id)
  {
    global $db;
    return $db->read()->get("SELECT * FROM post WHERE user_id=?", $id)[0];
  }

  public static function total($args=[])
  {
    global $db;
    $where = self::where($args);
    return $db->read()->value("SELECT COUNT(*) FROM post WHERE $where;");
  }

  public static function getLatest($n=8)
  {
    return self::getPosts(['posts'=>$n,'from'=>0]);
  }

  public static function getPosts($args=[])
  {
    global $db;
    $ppp = isset($args['posts'])?$args['posts']:8;
    $where = self::where($args);
    $start_from = isset($args['from'])?$args['from']:0;
    if (isset($args['page'])) {
      $start_from = ($args['page']-1)*$ppp;
    }

    $ql = "SELECT id,title,description,slug,SUBSTRING(post,1,300) as post,updated,user_id,
      (SELECT value FROM postmeta WHERE post_id=post.id AND vartype='thumbnail') as img,
      (SELECT GROUP_CONCAT('{', CONCAT('\"',value,'\":\"',title,'\"'), '}' SEPARATOR ',') FROM postmeta,postcategory p WHERE post_id=post.id AND vartype='category' AND value=p.id) as categories,
      (SELECT username FROM user WHERE post.user_id=id) as author
      FROM post
      WHERE $where
      ORDER BY id DESC LIMIT $start_from,$ppp";
    $res = $db->read()->query($ql);
    if ($res) {
      while ($r = mysqli_fetch_assoc($res)) {
        $r['url'] = Config::make_url('blog', '', ['p'=>$r['id'],'slug'=>$r['slug']]);
        yield $r;
      }
    }
  }

  public static function where($args=[])
  {
    $category = !empty($args['category'])?"AND id IN(SELECT post_id from postmeta where vartype='category' and value='{$args['category']}')":"";
    $tag = isset($args['tag'])?"AND id IN(SELECT post_id from postmeta where vartype='tag' and value='{$args['tag']}')":"";
    $user_id = isset($args['user_id'])?"AND user_id='{$args['user_id']}'":"";
    return "publish=1 $category $tag $user_id";
  }

  public static function search($s)
  {
    global $db;
    $res = $db->query("SELECT id,description,title,slug,SUBSTRING(post,1,300) as post,
      (SELECT value FROM postmeta WHERE post_id=post.id AND vartype='thumbnail') as img
      FROM post WHERE publish=1
      AND match(title,post) AGAINST(? IN NATURAL LANGUAGE MODE) ORDER BY id DESC", $s);
    if ($res) {
      while ($r = mysqli_fetch_array($res)) {
        yield $r;
      }
    }
  }

  public static function categories()
  {
    global $db;
    return $db->get("SELECT id,title FROM postcategory;");
  }
}
