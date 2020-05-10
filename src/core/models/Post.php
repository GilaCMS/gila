<?php
namespace core\models;
use Gila;

class Post
{
  static function getById($id)
  {
    return self::getByIdSlug($id);
  }

  static function getByIdSlug($id)
  {
    $db = Gila::slaveDB();
    $ql="SELECT id,description,title,post,publish,slug,updated,user_id,
      (SELECT a.value FROM postmeta a WHERE a.post_id=post.id AND vartype='thumbnail') as img,
      (SELECT GROUP_CONCAT(b.value SEPARATOR ',') FROM postmeta b WHERE b.post_id=post.id AND vartype='tag') as tags
      FROM post WHERE (id=? OR slug=?)";
    $res = $db->query($ql,[$id,$id]);
    if($row = mysqli_fetch_array($res)) {
      if($blocks = $db->value("SELECT blocks FROM post WHERE (id=? OR slug=?);", [$id,$id])) {
        $blocks = json_decode($blocks);
        ob_start();
        \View::blocks($blocks);
        $out = ob_get_contents();
        ob_end_clean();
        $row['post'] .= $out;
      }
      $row['url'] = Gila::make_url('blog','',['p'=>$row['id'],'slug'=>$row['slug']]);
      return $row;
    }
    return false;
  }

  static function meta($id, $meta, $value = null)
  {
    global $db;
    if ($value==null) {
      $ql = "SELECT value FROM postmeta where post_id=? and vartype=?;";
      return $db->getList($ql,[$id, $meta]);
    }
    $ql = "INSERT INTO postmeta(post_id,vartype,value) VALUES('?','?','?');";
    return $db->query($ql,[$id, $meta, $value]);
  }

  static function getMeta($meta)
  {
    $db = Gila::slaveDB();
    $ql = "SELECT value,COUNT(*) AS count FROM postmeta where vartype=? GROUP BY value;";
    return $db->get($ql,[$meta]);
  }

  static function getByUserID($id)
  {
    $db = Gila::slaveDB();
    return $db->get("SELECT * FROM post WHERE user_id=?",$id)[0];
  }

  static function total ($args=[])
  {
    $db = Gila::slaveDB();
    $where = self::where($args);
    return $db->value("SELECT COUNT(*) FROM post WHERE $where;");
  }

  static function getLatest($n=8)
  {
    return self::getPosts(['posts'=>$n,'from'=>0]);
  }

  static function getPosts ($args=[])
  {
    $db = Gila::slaveDB();
    $ppp = isset($args['posts'])?$args['posts']:8;
    $where = self::where($args);
    $start_from = isset($args['from'])?$args['from']:0;
    if(isset($args['page'])) $start_from = ($args['page']-1)*$ppp;

    $ql = "SELECT id,title,description,slug,SUBSTRING(post,1,300) as post,updated,user_id,
      (SELECT value FROM postmeta WHERE post_id=post.id AND vartype='thumbnail') as img,
      (SELECT GROUP_CONCAT('{', CONCAT('\"',value,'\":\"',title,'\"'), '}' SEPARATOR ',') FROM postmeta,postcategory p WHERE post_id=post.id AND vartype='category' AND value=p.id) as categories,
      (SELECT username FROM user WHERE post.user_id=id) as author
      FROM post
      WHERE $where
      ORDER BY id DESC LIMIT $start_from,$ppp";
    $res = $db->query($ql);
    if ($res) while ($r = mysqli_fetch_assoc($res)) {
      $r['url'] = Gila::make_url('blog','',['p'=>$r['id'],'slug'=>$r['slug']]);
      yield $r;
    }
  }

  static function where ($args=[]) {
    $category = isset($args['category'])?"AND id IN(SELECT post_id from postmeta where vartype='category' and value='{$args['category']}')":"";
    $tag = isset($args['tag'])?"AND id IN(SELECT post_id from postmeta where vartype='tag' and value='{$args['tag']}')":"";
    $user_id = isset($args['user_id'])?"AND user_id='{$args['user_id']}'":"";
    return "publish=1 $category $tag $user_id";
  }

  static function search ($s) {
    $db = Gila::slaveDB();
    $res = $db->query("SELECT id,description,title,slug,SUBSTRING(post,1,300) as post,
      (SELECT value FROM postmeta WHERE post_id=post.id AND vartype='thumbnail') as img
      FROM post WHERE publish=1
      AND match(title,post) AGAINST(? IN NATURAL LANGUAGE MODE) ORDER BY id DESC", $s);
    if ($res) while ($r = mysqli_fetch_array($res)) {
      yield $r;
    }
  }

  static function categories() {
    $db = Gila::slaveDB();
    return $db->get("SELECT id,title FROM postcategory;");
  }

}
