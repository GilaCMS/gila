<?php
namespace core\models;

class user
{

    function __construct ()
    {

    }

    static function create($email, $password, $name = '')
    {
        global $db;
        $pass = \gila::hash($password);
        $db->query("INSERT INTO user(email,pass,username) VALUES(?,?,?);",[$email, $pass, $name]);
        return $db->insert_id;
    }

    static function meta($id, $meta, $value = null)
    {
        global $db;
        if ($value==null) {
            $ql = "SELECT `value` FROM usermeta where user_id=? and vartype=? LIMIT 1;";
            return $db->value($ql,[$id, $meta]);
        }
        $ql = "INSERT INTO usermeta(user_id,vartype,value) VALUES(?,?,?);";
        return $db->query($ql,[$id, $meta, $value]);
    }

    static function metaList($id, $meta, $values = null)
    {
        global $db;
        if ($values==null) {
            $ql = "SELECT value FROM usermeta where user_id=? and vartype=?;";
            return $db->getList($ql,[$id, $meta]);
        }
        //// todo
    }

    static function getIdsByMeta($vartype,$value)
    {
        global $db;
        return $db->getList("SELECT user_id FROM usermeta WHERE value=? AND vartype='$vartype';",[$value]);
    }

    static function getByEmail($email)
    {
        global $db;
        $res = $db->get("SELECT * FROM user WHERE email=?",$email);
        if($res) return $res[0];
        return false;
    }

    static function getById($id)
    {
        global $db;
        $res = $db->get("SELECT * FROM user WHERE id=?",$id);
        if($res) return $res[0];
        return false;
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

    static function updateName($id,$name)
    {
        global $db;
        if(\session::key('user_id')==$id) \session::key('user_name',$name);
        return $db->query("UPDATE user SET username=? where id=?;",[$name,$id]);
    }
}
