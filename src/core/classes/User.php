<?php
namespace Gila;

class User
{
  private static $user = [];

  public static function create($email, $password, $name = '', $active = 0)
  {
    if (Event::get('validateUserPassword', true, $password)===true) {
      $pass = ($password===null)? '': Config::hash($password);
      if (DB::value("SELECT COUNT(*) FROM user WHERE email=?", [$email])>0) {
        return false;
      }
      $userId = DB::create('user', [
        'email'=>$email,'pass'=>$pass,'username'=>$name,'active'=>$active,'language'=>Config::lang()
      ]);
      Event::fire('User::create', ['userId'=>$userId]);
      return $userId;
    } else {
      return false;
    }
  }

  public static function meta($id, $meta, $value = null, $multi = false)
  {
    if ($value===null) {
      $ql = "SELECT `value` FROM usermeta where user_id=? and vartype=? ORDER BY id DESC LIMIT 1;";
      return DB::value($ql, [$id, $meta]);
    }
    if ($multi==false) {
      if (DB::value("SELECT COUNT(*) FROM usermeta WHERE user_id=? AND vartype=?;", [$id, $meta])) {
        $ql = "UPDATE usermeta SET `value`=? WHERE user_id=? AND vartype=?;";
        return DB::query($ql, [$value, $id, $meta]);
      }
    }
    $ql = "INSERT INTO usermeta(user_id,vartype,`value`) VALUES(?,?,?);";
    return DB::query($ql, [$id, $meta, $value]);
  }

  public static function metaDelete($id, $meta, $value=null)
  {
    if ($value===null) {
      DB::query("DELETE FROM usermeta WHERE user_id=? AND vartype=?", [$id, $meta]);
    } else {
      DB::query("DELETE FROM usermeta WHERE user_id=? AND vartype=? AND `value`=?", [$id, $meta, $value]);
    }
  }

  public static function metaList($id, $meta, $values = null)
  {
    if ($values===null) {
      $ql = "SELECT `value` FROM usermeta where user_id=? and vartype=?;";
      return DB::getList($ql, [$id, $meta]);
    }

    if (!is_array($values)) {
      return false;
    }

    self::metaDelete($id, $meta);
    foreach ($values as $value) {
      $ql = "INSERT INTO usermeta(user_id,vartype,value) VALUES(?,?,?);";
      DB::query($ql, [$id, $meta, $value]);
    }
    return true;
  }

  public static function getIdsByMeta($vartype, $value)
  {
    return DB::getList("SELECT user_id FROM usermeta WHERE value=? AND vartype='$vartype';", [$value]);
  }

  public static function getIdsWithPermission($permission)
  {
    $rp = Config::getArray('permissions');
    $roles = [];
    foreach ($rp as $role=>$row) {
      foreach ($row as $perm) {
        if ($permission==$perm && !in_array($role, $roles)) {
          $roles[] = $role;
        }
      }
    }
    $values = implode(',', $roles);
    return DB::getList("SELECT user_id FROM usermeta WHERE value IN({$values}) AND vartype='role';");
  }

  public static function getByMeta($key, $value)
  {
    $res = DB::get("SELECT * FROM user WHERE id=(SELECT user_id FROM usermeta WHERE vartype=? AND value=? LIMIT 1)", [$key, $value]);
    if ($res) {
      return $res[0];
    }
    return false;
  }

  public static function getByEmail($email)
  {
    $res = DB::get("SELECT * FROM user WHERE email=?", $email);
    if ($res) {
      return $res[0];
    }
    return false;
  }

  public static function getById($id)
  {
    if (!isset(self::$user[$id])) {
      self::$user[$id] = DB::getOne("SELECT * FROM user WHERE id=?", $id);
    }
    return self::$user[$id];
  }

  public static function getByResetCode($rp)
  {
    $user_id = DB::value("SELECT user_id FROM usermeta WHERE vartype='reset_code' AND value=?;", $rp);
    if (!$user_id) {
      return false;
    }
    return DB::get("SELECT * FROM user WHERE id='$user_id';")[0];
  }

  public static function updatePassword($id, $pass)
  {
    if (Event::get('validateUserPassword', true, $pass)===true) {
      DB::query("UPDATE user SET pass=? WHERE id=?;", [Config::hash($pass),$id]);
      return true;
    } else {
      return false;
    }
  }

  public static function updateName($id, $name)
  {
    if (Session::key('user_id')==$id) {
      Session::key('user_name', $name);
    }
    return DB::query("UPDATE user SET username=? WHERE id=?;", [$name,$id]);
  }

  public static function updateActive($id, $value)
  {
    return DB::query("UPDATE user SET active=? WHERE id=?;", [$value, $id]);
  }

  public static function permissions($id)
  {
    $response = [];
    $rp = Config::getArray('permissions')??[];
    if ($id === 0) {
      return [];
    }
    $roles = self::metaList($id, 'role');
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
    return $response;
  }

  public static function logoutFromDevice($n)
  {
    $sessions = Session::findByUserId(Session::userId());
    if (!isset($sessions[$n])) {
      return false;
    }
    Session::remove($sessions[$n]['gsessionid']);
    return true;
  }

  public static function level($id)
  {
    return DB::value("SELECT MAX(userrole.level) FROM userrole,usermeta
    WHERE userrole.id=usermeta.value AND usermeta.vartype='role' AND usermeta.user_id=?", $id) ?? 0;
  }

  public static function roleLevel($id)
  {
    return DB::value("SELECT userrole.level FROM userrole WHERE id=?", $id) ?? 0;
  }

  public static function sendInvitation($data)
  {
    Config::addLang('core/lang/login/');
    $reset_code = substr(bin2hex(random_bytes(50)), 0, 50);
    $baseurl = Config::base('user/password_reset');
    $reset_url = $baseurl.'?rp='.$reset_code;
    self::meta($data['id'], 'reset_code', $reset_code);
    if (Event::get(
      'user_invite.email',
      false,
      ['user'=>$data, 'reset_code'=>$reset_code, 'reset_url'=>$reset_url]
    )) {
      return;
    }

    $subject = __('invite_msg_ln1').' '.$data['username'];
    $msg = __('invite_msg_ln2')." {$data['username']}\n\n";
    $msg .= __('invite_msg_ln3').' '.Config::get('title')."\n\n";
    $msg .= __('invite_msg_ln4')."\n";
    $msg .= $reset_url."\n\n";
    $msg .= __('activate_msg_ln4');
    $headers = "From: ".Config::get('title')." <noreply@{$_SERVER['HTTP_HOST']}>";
    new Sendmail(['email'=>$data['email'], 'subject'=>$subject, 'message'=>$msg, 'headers'=>$headers]);
  }

  public static function register($data)
  {
    if (Event::get('recaptcha', true)===false) {
      View::alert('error', __('_recaptcha_error'));
      return false;
    }
    if ($error = Event::get('register.error', null, $data)) {
      View::alert('error', $error);
      return false;
    }

    $email = $data['email'] ?? Request::key('email');
    $name = $data['name'] ?? Request::key('name');
    $password = $data['password'];
    Config::addLang('core/lang/myprofile/');

    if (strlen($password)<5) {
      View::alert('alert', __('New password too small'));
      return false;
    }

    if ($name != $data['name']) {
      View::alert('error', __('register_error2'));
    } elseif (self::getByEmail($email) || $email != $data['email']) {
      View::alert('error', __('register_error1'));
    } else {
      // register the user
      $active = Config::get('user_activation')=='auto'? 1: 0;
      if ($userId = self::create($email, $password, $name, $active)) {
        // success
        if (Config::get('user_activation')=='byemail') {
          $activate_code = substr(bin2hex(random_bytes(50)), 0, 50);
          $baseactivate = Config::base('user/activate');
          $activate_url = $baseactivate.'?ap='.$activate_code;
          $data = [
            'user'=>['id'=>$userId, 'username'=>$name, 'email'=>$email],
            'activate_code'=>$activate_code, 'activate_url'=>$activate_url
          ];
          if (!Event::get('user_activation.email', false, $data)) {
            $subject = __('activate_msg_ln1').' '.$name;
            $msg = __('activate_msg_ln2')." {$name}\n\n";
            $msg .= __('activate_msg_ln3').' '.Config::get('title')."\n\n";
            $msg .= $activate_url."\n\n";
            $msg .= __('activate_msg_ln4');
            $headers = "From: ".Config::get('title')." <noreply@{$_SERVER['HTTP_HOST']}>";
            new Sendmail(['email'=>$email, 'subject'=>$subject, 'message'=>$msg, 'headers'=>$headers]);
          }
          self::meta($userId, 'activate_code', $activate_code);
        }
        return true;
      } else {
        View::alert('error', 'user is '.$userId);
        // View::alert('error', __('register_error2'));
      }
    }
    return false;
  }
}
