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
    $ql = "SELECT id,description,title,post,`language`,publish,slug,created,updated,user_id,
      (SELECT a.value FROM postmeta a WHERE a.post_id=post.id AND vartype='thumbnail') as img,
      (SELECT GROUP_CONCAT(b.value SEPARATOR ',') FROM postmeta b WHERE b.post_id=post.id AND vartype='tag') as tags
      FROM post WHERE (id=? OR slug=?)";
    $res = DB::query($ql, [$id,$id]);
    if ($row = mysqli_fetch_array($res)) {
      if ($blocks = DB::value("SELECT blocks FROM post WHERE (id=? OR slug=?);", [$id,$id])) {
        $blocks = json_decode($blocks);
        $row['post'] .= View::blocks($blocks, 'post'.$row['id']);
      }
      $row['url'] = Config::url('blog', ['p'=>$row['id'],'slug'=>$row['slug']]);
      return $row;
    }
    return false;
  }

  public static function meta($id, $meta, $value = null)
  {
    if ($value==null) {
      $ql = "SELECT value FROM postmeta where post_id=? and vartype=?;";
      return DB::getList($ql, [$id, $meta]);
    }
    $ql = "INSERT INTO postmeta(post_id,vartype,value) VALUES('?','?','?');";
    return DB::query($ql, [$id, $meta, $value]);
  }

  public static function getMeta($meta)
  {
    $ql = "SELECT value,COUNT(*) AS count FROM postmeta where vartype=? GROUP BY value;";
    return DB::get($ql, [$meta]);
  }

  public static function getByUserID($id)
  {
    return DB::get("SELECT * FROM post WHERE user_id=?", $id)[0];
  }

  public static function total($args=[])
  {
    $where = self::where($args);
    return DB::value("SELECT COUNT(*) FROM post WHERE $where;");
  }

  public static function getLatest($n=8)
  {
    return self::getPosts(['posts'=>$n,'from'=>0]);
  }

  public static function getPosts($args=[])
  {
    $ppp = isset($args['posts'])?$args['posts']:8;
    $where = self::where($args);
    $start_from = isset($args['from'])?$args['from']:0;
    if (isset($args['page'])) {
      $start_from = ($args['page']-1)*$ppp;
    }

    $ql = "SELECT id,title,description,slug,SUBSTRING(post,1,300) as post,created,updated,user_id,
      (SELECT value FROM postmeta WHERE post_id=post.id AND vartype='thumbnail') as img,
      (SELECT GROUP_CONCAT('{', CONCAT('\"',value,'\":\"',title,'\"'), '}' SEPARATOR ',') FROM postmeta,postcategory p WHERE post_id=post.id AND vartype='category' AND value=p.id) as categories,
      (SELECT username FROM user WHERE post.user_id=id) as author, `language`
      FROM post
      WHERE $where
      ORDER BY id DESC LIMIT $start_from,$ppp";
    $rows = DB::get($ql);
    foreach ($rows as $r) {
      $r['url'] = Config::url('blog/'.$r['id'].'/'.$r['slug']);
      $r['post'] = HtmlInput::DOMSanitize($r['post'], false);
      yield $r;
    }
  }

  public static function where($args=[])
  {
    $category = !empty($args['category'])?"AND id IN(SELECT post_id from postmeta where vartype='category' and value='{$args['category']}')":"";
    $tag = isset($args['tag'])?"AND id IN(SELECT post_id from postmeta where vartype='tag' and value='{$args['tag']}')":"";
    $user_id = isset($args['user_id'])?"AND user_id='{$args['user_id']}'":"";
    $language = isset($args['language'])?"AND (language='{$args['language']}' OR language IS NULL)":"";
    return "publish=1 $category $tag $user_id $language";
  }

  public static function search($s)
  {
    $res = DB::query("SELECT id,description,title,slug,SUBSTRING(post,1,300) as post,
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
    return DB::get("SELECT id,title FROM postcategory;");
  }


  public static function inCachedList($id)
  {
    $array = Cache::remember('post_cache_list', 86400, function ($u) {
      $ql = "SELECT id FROM post WHERE publish=1 UNION SELECT slug FROM post WHERE publish=1";
      return json_encode(DB::getList($ql));
    }, [Config::mt('post')]);
    return (in_array($id, json_decode($array, true)));
  }
}
