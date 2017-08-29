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
            $ql = "SELECT value FROM postmeta where user_id=? and vartype=?;";
            return $db->value($ql,[$id, $meta]);
        }
        $ql = "INSERT INTO postmeta(user_id,vartype,value) VALUES('?','?','?');";
        return $db->query($ql,[$id, $meta, $value]);
    }

    static function getByUserID($id)
    {
        global $db;
        return $db->get("SELECT * FROM post WHERE user_id=?",$id)[0];
    }

    static function total ()
    {
        global $db;
/*        $res = $db->query("SELECT * FROM post WHERE publish=1;");

        if ($r = mysqli_fetch_array($res)) {
            return $r[0];
        } else return 0;
*/
        return $db->value("SELECT * FROM post WHERE publish=1;");
    }

    static function getLatest($n=8)
    {
        return self::getPosts(['posts'=>$n]);
/*        global $db;
        $res = $db->query("SELECT id,title,slug,SUBSTRING(post,1,300) as post,updated,
            (SELECT value FROM postmeta WHERE post_id=post.id AND vartype='thumbnail') as img
            FROM post ORDER BY id DESC LIMIT 0,$n");

        if ($res) while ($r = mysqli_fetch_object($res)) {
            yield $r;
        }*/
    }

    static function getPosts ($args=[])
    {
        global $db;
        $ppp = isset($args['posts'])?$args['posts']:8;
        $category = isset($args['category'])?"AND '{$args['category']}' IN(SELECT `value` from postmeta where vartype='category' and post_id=post.id)":"";
        $tag = isset($args['tag'])?"AND id IN(SELECT post_id from postmeta where vartype='tag' and value='{$args['tag']}')":"";
        $start_from = (\blog::$page-1)*$ppp;
        $where = '';

        $ql = "SELECT id,title,slug,SUBSTRING(post,1,300) as post,updated,
            (SELECT value FROM postmeta WHERE post_id=post.id AND vartype='thumbnail') as img
            FROM post
            WHERE publish=1 $category $tag
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
/*
    static function getPostArray ($args=[])
    {
        global $db;
        $ppp = isset($args['posts'])?$args['posts']:8;
        $start_from = (\blog::$page-1)*$ppp;
        $where = '';

        $res = $db->query("SELECT id,title,slug,SUBSTRING(post,1,300) as post,updated,
            (SELECT value FROM postmeta WHERE post_id=post.id AND vartype='thumbnail') as img
            FROM post WHERE publish=1 ORDER BY id DESC LIMIT $start_from,$ppp");

        if ($res) while ($r = mysqli_fetch_array($res)) {
            yield $r;
        }
    }

    static function getByTag ($args = []) {
        global $db;
        $ppp = isset($args['posts'])?$args['posts']:8;
        $tag = isset($args['tag'])?$args['tag']:'';
        $start_from = (\blog::$page-1)*$ppp;
        $where = '';

        $ql="SELECT id,title,SUBSTRING(post,1,300) as post,slug,updated,
            (SELECT value FROM postmeta WHERE post_id=post.id AND vartype='thumbnail') as img
            FROM post WHERE publish=1
            AND id IN(SELECT post_id from postmeta where vartype='tag' and value='$tag')
            ORDER BY id DESC LIMIT $start_from,$ppp";
        $res = $db->query($ql);


        if ($res) while ($r = mysqli_fetch_assoc($res)) {
            yield $r;
        }
    }
*/

}
