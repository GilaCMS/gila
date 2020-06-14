<?php
namespace core\models;
use Gila;
use Session;
use Event;

class user
{

  static function create($email, $password, $name = '', $active = 1)
  {
    global $db;
    if( Event::get('validateUserPassword', true, $password)===true) {
      $pass = ($password===null)? '': Gila::hash($password);
      $db->query("INSERT INTO user(email,pass,username,active)
        VALUES(?,?,?,?);",[$email, $pass, $name, $active]);
      return $db->insert_id;
    } else return false;
  }

  static function meta($id, $meta, $value = null, $multi = false)
  {
    global $db;
    if ($value===null) {
      $ql = "SELECT `value` FROM usermeta where user_id=? and vartype=? LIMIT 1;";
      return $db->value($ql,[$id, $meta]);
    }
    if($multi==false) if($db->value("SELECT COUNT(*) FROM usermeta WHERE user_id=? AND vartype=?;",[$id, $meta])) {
      $ql = "UPDATE usermeta SET `value`=? WHERE user_id=? AND vartype=?;";
      return $db->query($ql,[$value, $id, $meta]);
    }
    $ql = "INSERT INTO usermeta(user_id,vartype,`value`) VALUES(?,?,?);";
    return $db->query($ql,[$id, $meta, $value]);
  }

  static function metaDelete($id, $meta, $value=null) {
    global $db;
    if($value===null) {
      $db->query("DELETE FROM usermeta WHERE user_id=? AND vartype=?", [$id, $meta]);
    } else {
      $db->query("DELETE FROM usermeta WHERE user_id=? AND vartype=? AND `value`=?", [$id, $meta, $value]);
    }
  }

  static function metaList($id, $meta, $values = null)
  {
    $db = Gila::slaveDB();
    if ($values===null) {
      $ql = "SELECT value FROM usermeta where user_id=? and vartype=?;";
      return $db->getList($ql,[$id, $meta]);
    }

    if(!is_array($values)) return false;

    self::metaDelete($id, $meta);
    foreach($values as $value) {
      $ql = "INSERT INTO usermeta(user_id,vartype,value) VALUES(?,?,?);";
      $db->query($ql,[$id, $meta, $value]);
    }
    return true;
  }

  static function getIdsByMeta($vartype,$value)
  {
    global $db;
    return $db->getList("SELECT user_id FROM usermeta WHERE value=? AND vartype='$vartype';", [$value]);
  }

  static function getByMeta($key, $value)
  {
    $db = Gila::slaveDB();
    $res = $db->get("SELECT * FROM user WHERE id=(SELECT user_id FROM usermeta WHERE vartype=? AND value=? LIMIT 1)", [$key, $value]);
    if($res) return $res[0];
    return false;
  }

  static function getByEmail($email)
  {
    $db = Gila::slaveDB();
    $res = $db->get("SELECT * FROM user WHERE email=?", $email);
    if($res) return $res[0];
    return false;
  }

  static function getById($id)
  {
    $db = Gila::slaveDB();
    $res = $db->get("SELECT * FROM user WHERE id=?", $id);
    if($res) return $res[0];
    return false;
  }

  static function getByResetCode($rp)
  {
    $db = Gila::slaveDB();
    $user_id = $db->value("SELECT user_id FROM usermeta where vartype='reset_code' and value=?;",$rp);
    echo $user_id;
    if(!$user_id) return false;
    return $db->get("SELECT * FROM user where id='$user_id';")[0];
  }

  static function updatePassword($id,$pass)
  {
    global $db;
    if( Event::get('validateUserPassword', true, $pass)===true) {
      $db->query("UPDATE user SET pass=? where id=?;",[Gila::hash($pass),$id]);
      return true;
    } else return false;
  }

  static function updateName($id,$name)
  {
    global $db;
    if(Session::key('user_id')==$id) Session::key('user_name',$name);
    return $db->query("UPDATE user SET username=? where id=?;",[$name,$id]);
  }

  static function updateActive($id, $value)
  {
    global $db;
    return $db->query("UPDATE user SET active=? where id=?;",[$value, $id]);
  }

  static function permissions($id) {
    if($id == 0) {
      if(Session::key('permissions')) return Session::key('permissions');
    }

    $response = User::metaList( $id, 'privilege'); // DEPRECATED since 1.9.0
    $roles = User::metaList($id, 'role');
    $rp = Gila::config('permissions');
    if($id != 0) {
      foreach($roles as $role) if(isset($rp[$role])) foreach($rp[$role] as $perm) {
        if(!in_array($perm, $response)) $response[] = $perm;
      }
      if(isset($rp['member'])) $response = array_merge($response, $rp['member']);
    } else {
      if(isset($rp[0])) $response = $rp['0']; else $response = [];
      Session::key('permissions',$response);
    }
    return $response;
  }

  static function logoutFromDevice($n) {
    global $db;
    $sessions = User::metaList(Session::userId(), 'GSESSIONID');
    if(!isset($sessions[$n])) return false;
    $db->query("DELETE FROM usermeta WHERE `vartype`='GSESSIONID' AND `value`=?;",
      $sessions[$n]);
    @unlink(LOG_PATH.'/sessions/'.$sessions[$n]);
    return true;
  }
}
