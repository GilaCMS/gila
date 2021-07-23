<?php
namespace Gila;

class Profile
{
  public static function postUpdate($user_id)
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      return;
    }

    if (Router::post('submit-btn')==='submited') {
      User::updateName($user_id, strip_tags($_POST['gila_username']));
      if (isset($_POST['meta'])) {
        foreach ($_POST['meta'] as $meta=>$metavalue) {
          User::meta($user_id, $meta, strip_tags($metavalue));
        }
      }
      if ($_POST['gila_photo']==null) {
        User::metaDelete($user_id, 'photo');
      } else {
        User::meta($user_id, 'photo', strip_tags($_POST['gila_photo']));
      }
      View::alert('success', __('_changes_updated'));
    }

    if (Router::post('submit-btn')=='password') {
      $usr = User::getById($user_id);
      $pass = $_POST['new_pass'];
      if (password_verify($_POST['old_pass'], $usr['pass'])) {
        if (strlen($pass) > 4) {
          if ($pass===$_POST['new_pass2']) {
            if (User::updatePassword($user_id, $pass)) {
              View::alert('success', __('_changes_updated'));
            }
          } else {
            View::alert('alert', __('New passwords do not match'));
          }
        } else {
          View::alert('alert', __('New password too small'));
        }
      } else {
        View::alert('alert', __('Password incorrect'));
      }
    }

    if (Router::post('token')==='generate') {
      $token = self::generateToken();
      while (count(User::getIdsByMeta('token', $token)) > 0) {
        $token = self::generateToken();
      }
      User::meta($user_id, 'token', $token);
      View::alert('success', __('_changes_updated'));
    }

    if (Router::post('token')==='delete') {
      User::meta($user_id, 'token', '');
      View::alert('success', __('_changes_updated'));
    }
  }

  public static function generateToken()
  {
    $token = '';
    while (strlen($token) < 160) {
      $token .= hash('sha512', uniqid(true));
    }
    return substr($token, 0, 160);
  }

  public static function getPermissions($user_id) // DEPRECATED
  {
    $allPermissions = self::getAllPermissions();
    $userPermissions = User::permissions($user_id);
    $permissions = [];
    foreach ($allPermissions as $key=>$txt) {
      if (in_array($key, $userPermissions)) {
        $permissions[$key] = $txt;
      }
    }
    return $permissions;
  }

  public static function getAllPermissions()
  {
    $permissions = [];
    $packages = array_merge(Config::packages(), ["core"]);
    foreach ($packages as $package) {
      $pjson = 'src/'.$package.'/package.json';
      $perjson = 'src/'.$package.'/package/en.json';
      if (file_exists($pjson)) {
        $parray = json_decode(file_get_contents($pjson), true);
        if (isset($parray['permissions'])) {
          $permissions = array_merge($permissions, $parray['permissions']);
          if (isset($parray['lang'])) {
            Config::addLang($parray['lang']);
          }
          Config::addLang($package.'/lang/permissions/');
        }
      }
    }
    return $permissions;
  }
}
