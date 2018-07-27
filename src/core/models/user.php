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

    static function meta($id, $meta, $value = null, $multi = false)
    {
        global $db;
        if ($value==null) {
            $ql = "SELECT `value` FROM usermeta where user_id=? and vartype=? LIMIT 1;";
            return $db->value($ql,[$id, $meta]);
        }
        if($multi==false) if($db->value("SELECT COUNT(*) FROM usermeta WHERE user_id=? AND vartype=?;",[$id, $meta])) {
              $ql = "UPDATE usermeta SET `value`=? WHERE user_id=? AND vartype=?;";
              return $db->query($ql,[$value, $id, $meta]);
        }
        $ql = "INSERT INTO usermeta(user_id,vartype,`value`) VALUES(?,?,?);";
        return $db->query($ql,[$id, $meta, $value, $value]);
    }

    static function metaList($id, $meta, $values = null)
    {
        global $db;
        if ($values==null) {
            $ql = "SELECT value FROM usermeta where user_id=? and vartype=?;";
            return $db->getList($ql,[$id, $meta]);
        }

        if(!is_array($values)) return false;

        $db->query("DELETE FROM usermeta WHERE user_id=? AND vartype=?",[$id, $meta]);
        foreach($values as $value) {
            $ql = "INSERT INTO usermeta(user_id,vartype,value) VALUES(?,?,?);";
            $db->query($ql,[$id, $meta, $value]);
        }
        return true;
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

    static function permissions($id) {
        if($id == 0) {
            if(\session::key('permissions')) return \session::key('permissions');
        }

        $response = user::metaList( $id, 'privilege');
        $file = 'log/permissions.json';
        if(file_exists($file)) {
            $roles = user::metaList( $id, 'group');
            $rp = json_decode(file_get_contents($file),true);
            if($id != 0) {
                foreach($roles as $role) if(isset($rp[$role]))
                    $response = array_merge($response, $rp[$role]);
                if(isset($rp['member'])) $response = array_merge($response, $rp['member']);
            } else {
                if(isset($rp[0])) $response = $rp['0']; else $response = [];
                \session::key('permissions',$response);
            }
        }
        return $response;
    }
}
