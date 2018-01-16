<?php
namespace core\models;

class post
{

    function __construct ()
    {
        //\blog::$page = (router::get('page',1))?:1;
    }

    static function meta($id, $meta, $value = null)
    {
        global $db;
        if ($value==null) {
            $ql = "SELECT value FROM postmeta where post_id=? and vartype=?;";
            return $db->value($ql,[$id, $meta]);
        }
        $ql = "INSERT INTO postmeta(post_id,vartype,value) VALUES('?','?','?');";
        return $db->query($ql,[$id, $meta, $value]);
    }

    static function getMeta($meta)
    {
        global $db;
        $ql = "SELECT value,COUNT(*) AS count FROM postmeta where vartype=? GROUP BY value;";
        return $db->get($ql,[$meta]);
    }

    static function getByUserID($id)
    {
        global $db;
        return $db->get("SELECT * FROM post WHERE user_id=?",$id)[0];
    }

    static function total ()
    {
        global $db;
        return $db->value("SELECT COUNT(*) FROM post WHERE publish=1;");
    }

    static function getLatest($n=8)
    {
        return self::getPosts(['posts'=>$n]);
    }

    static function getPosts ($args=[])
    {
        global $db;
        $ppp = isset($args['posts'])?$args['posts']:8;
        $category = isset($args['category'])?"AND id IN(SELECT post_id from postmeta where vartype='category' and value='{$args['category']}')":"";
        $tag = isset($args['tag'])?"AND id IN(SELECT post_id from postmeta where vartype='tag' and value='{$args['tag']}')":"";
        $user_id = isset($args['user_id'])?"AND user_id='{$args['user_id']}'":"";
        $start_from = (\blog::$page-1)*$ppp?:0;
        $where = '';

        $ql = "SELECT id,title,slug,SUBSTRING(post,1,300) as post,updated,user_id,
            (SELECT value FROM postmeta WHERE post_id=post.id AND vartype='thumbnail') as img,
            (SELECT username FROM user WHERE post.user_id=id) as author
            FROM post
            WHERE publish=1 $category $tag $user_id
            ORDER BY id DESC LIMIT $start_from,$ppp";
        $res = $db->query($ql);
        if ($res) while ($r = mysqli_fetch_assoc($res)) {
            yield $r;
        }
    }

    static function search ($s) {
        global $db;

        $res = $db->query("SELECT id,title,slug,SUBSTRING(post,1,300) as post,
            (SELECT value FROM postmeta WHERE post_id=post.id AND vartype='thumbnail') as img
            FROM post WHERE match(title,post) AGAINST('$s' IN NATURAL LANGUAGE MODE) ORDER BY id DESC");
        if ($res) while ($r = mysqli_fetch_array($res)) {
            yield $r;
        }
    }


}
