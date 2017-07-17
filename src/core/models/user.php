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

    function meta($id, $meta, $value = null)
    {
        global $db;
        if ($value==null) {
            $ql = "SELECT value FROM usermeta where user_id=? and vartype=?;";
            return $db->value($ql,[$id, $meta]);
        }
        $ql = "INSERT INTO usermeta(user_id,vartype,value) VALUES('?','?','?');";
        return $db->query($ql,[$id, $meta, $value]);
    }

    function getByEmail($email)
    {
        return $db->get("SELECT * FROM user WHERE email=?",$email);
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
        return $db->query("UPDATE user SET pass=? where id=?;",[password_hash($pass, PASSWORD_BCRYPT),$id]);
    }
}
