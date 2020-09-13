<?php
namespace Gila;

class User
{
  public static function create($email, $password, $name = '', $active = 1)
  {
    global $db;
    if (Event::get('validateUserPassword', true, $password)===true) {
      $pass = ($password===null)? '': Config::hash($password);
      $db->query("INSERT INTO user(email,pass,username,active)
        VALUES(?,?,?,?);", [$email, $pass, $name, $active]);
      return $db->insert_id;
    } else {
      return false;
    }
  }

  public static function meta($id, $meta, $value = null, $multi = false)
  {
    global $db;
    if ($value===null) {
      $ql = "SELECT `value` FROM usermeta where user_id=? and vartype=? LIMIT 1;";
      return $db->value($ql, [$id, $meta]);
    }
    if ($multi==false) {
      if ($db->value("SELECT COUNT(*) FROM usermeta WHERE user_id=? AND vartype=?;", [$id, $meta])) {
        $ql = "UPDATE usermeta SET `value`=? WHERE user_id=? AND vartype=?;";
        return $db->query($ql, [$value, $id, $meta]);
      }
    }
    $ql = "INSERT INTO usermeta(user_id,vartype,`value`) VALUES(?,?,?);";
    return $db->query($ql, [$id, $meta, $value]);
  }

  public static function metaDelete($id, $meta, $value=null)
  {
    global $db;
    if ($value===null) {
      $db->query("DELETE FROM usermeta WHERE user_id=? AND vartype=?", [$id, $meta]);
    } else {
      $db->query("DELETE FROM usermeta WHERE user_id=? AND vartype=? AND `value`=?", [$id, $meta, $value]);
    }
  }

  public static function metaList($id, $meta, $values = null)
  {
    global $db;
    if ($values===null) {
      $ql = "SELECT value FROM usermeta where user_id=? and vartype=?;";
      return $db->read()->getList($ql, [$id, $meta]);
    }

    if (!is_array($values)) {
      return false;
    }

    self::metaDelete($id, $meta);
    foreach ($values as $value) {
      $ql = "INSERT INTO usermeta(user_id,vartype,value) VALUES(?,?,?);";
      $db->query($ql, [$id, $meta, $value]);
    }
    return true;
  }

  public static function getIdsByMeta($vartype, $value)
  {
    global $db;
    return $db->getList("SELECT user_id FROM usermeta WHERE value=? AND vartype='$vartype';", [$value]);
  }

  public static function getByMeta($key, $value)
  {
    global $db;
    $res = $db->read()->get("SELECT * FROM user WHERE id=(SELECT user_id FROM usermeta WHERE vartype=? AND value=? LIMIT 1)", [$key, $value]);
    if ($res) {
      return $res[0];
    }
    return false;
  }

  public static function getByEmail($email)
  {
    global $db;
    $res = $db->read()->get("SELECT * FROM user WHERE email=?", $email);
    if ($res) {
      return $res[0];
    }
    return false;
  }

  public static function getById($id)
  {
    global $db;
    $res = $db->read()->get("SELECT * FROM user WHERE id=?", $id);
    if ($res) {
      return $res[0];
    }
    return false;
  }

  public static function getByResetCode($rp)
  {
    global $db;
    $user_id = $db->read()->value("SELECT user_id FROM usermeta where vartype='reset_code' and value=?;", $rp);
    echo $user_id;
    if (!$user_id) {
      return false;
    }
    return $db->read()->get("SELECT * FROM user where id='$user_id';")[0];
  }

  public static function updatePassword($id, $pass)
  {
    global $db;
    if (Event::get('validateUserPassword', true, $pass)===true) {
      $db->query("UPDATE user SET pass=? where id=?;", [Config::hash($pass),$id]);
      return true;
    } else {
      return false;
    }
  }

  public static function updateName($id, $name)
  {
    global $db;
    if (Session::key('user_id')==$id) {
      Session::key('user_name', $name);
    }
    return $db->query("UPDATE user SET username=? where id=?;", [$name,$id]);
  }

  public static function updateActive($id, $value)
  {
    global $db;
    return $db->query("UPDATE user SET active=? where id=?;", [$value, $id]);
  }

  public static function permissions($id)
  {
    if ($id == 0) {
      if (Session::key('permissions')) {
        return Session::key('permissions');
      }
    }

    if ($response = Cache::get('user-perm-'.$id, 3600, Config::config('updated'))) {
      return json_decode($response, true);
    }

    $response = User::metaList($id, 'privilege'); // DEPRECATED since 1.9.0
    $roles = User::metaList($id, 'role');
    $rp = Config::config('permissions');
    if ($id != 0) {
      foreach ($roles as $role) {
        if (isset($rp[$role])) {
          foreach ($rp[$role] as $perm) {
            if (!in_array($perm, $response)) {
              $response[] = $perm;
            }
          }
        }
      }
      if (isset($rp['member'])) {
        $response = array_merge($response, $rp['member']);
      }
    } else {
      if (isset($rp[0])) {
        $response = $rp['0'];
      } else {
        $response = [];
      }
      Session::key('permissions', $response);
    }
    Cache::set('user-perm-'.$id, json_encode($response), [Config::config('updated')]);
    return $response;
  }

  public static function logoutFromDevice($n)
  {
    global $db;
    $sessions = User::metaList(Session::userId(), 'GSESSIONID');
    if (!isset($sessions[$n])) {
      return false;
    }
    $db->query(
      "DELETE FROM usermeta WHERE `vartype`='GSESSIONID' AND `value`=?;",
      $sessions[$n]
    );
    @unlink(LOG_PATH.'/sessions/'.$sessions[$n]);
    return true;
  }

  public static function level($id)
  {
    global $db;
    return $db->value("SELECT MAX(userrole.level) FROM userrole,usermeta WHERE userrole.id=usermeta.value AND usermeta.user_id=?", $id) ?? 0;
  }

  public static function roleLevel($id)
  {
    global $db;
    return $db->value("SELECT userrole.level FROM userrole WHERE id=?", $id) ?? 0;
  }
}
