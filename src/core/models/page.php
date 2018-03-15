<?php
namespace core\models;

class page
{

    function __construct ()
    {

    }

    static function getById($id)
    {
        global $db;
        $res = $db->query("SELECT id,title,page,updated,publish,slug FROM page WHERE publish=1 AND id=?;",[$id]);
        if($res && $r = mysqli_fetch_array($res)) {
            return $r;
        }
        return false;
    }

    static function getObjById($id)
    {
        global $db;
        $res = $db->query("SELECT id,title,page,updated,publish,slug FROM page WHERE publish=1 AND id=?;",[$id]);
        if($res && $r = mysqli_fetch_object($res)) {
            return $r;
        }
        return false;
    }

    static function getByIdSlug($id)
    {
        global $db;
        $res = $db->query("SELECT id,title,page,updated,publish,slug FROM page WHERE publish=1 AND (id=? OR slug=?);",[$id,$id]);
        if($res && $r = mysqli_fetch_array($res)) {
            return $r;
        }
        return false;
    }

    static function genPublished()
    {
        global $db;
        $ql = "SELECT id,title,slug FROM page WHERE publish=1;";
        $res = $db->query($ql);
        while($r = mysqli_fetch_array($res)) {
            yield $r;
        }
        return;
    }

}
