<?php
namespace core\models;

class user
{

    function __construct ()
    {

    }

    function create($email, $password = null)
    {

    }

    static function meta($id, $meta, $value = null)
    {
        global $db;
        if ($value==null) {
            $ql = "SELECT value FROM usermeta where user_id=? and vartype=?;";
            return $db->value($ql,[$id, $meta]);
        }
        $ql = "INSERT INTO usermeta(user_id,vartype,value) VALUES('?','?','?');";
        return $db->query($ql,[$id, $meta, $value]);
    }

    static function getByEmail($email)
    {
        global $db;
        return $db->get("SELECT * FROM user WHERE email=?",$email)[0];
    }

    static function getByResetCode($rp)
    {
        global $db;
        $user_id = $db->value("SELECT user_id FROM usermeta where vartype='reset_code' and value=?;",$rp);
        echo $user_id;
        if(!$user_id) return false;
        return $db->get("SELECT * FROM user where id='$user_id';")[0];
    }

    static function updatePassword($id,$pass)
    {
        global $db;
        return $db->query("UPDATE user SET pass=? where id=?;",[\gila::hash($pass),$id]);
    }
}
