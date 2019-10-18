<?php
namespace core\models;

class profile
{

  static function postUpdate($user_id)
  {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

    if(!isset($_COOKIE['GSESSIONID']) ||
        $_COOKIE['GSESSIONID'] !== \session::key('GSESSIONID')) {
          echo $_COOKIE['GSESSIONID']."  ".\session::key('GSESSIONID');
      http_response_code(403);
      die('Access denied');
    }

    if (\router::post('submit-btn')=='submited'){
      user::updateName($user_id, strip_tags($_POST['gila_username']));
      user::meta($user_id, 'twitter_account', strip_tags($_POST['twitter_account']));
      \view::alert('success',__('_changes_updated'));
    }

    if (\router::post('submit-btn')=='password'){
      $usr = user::getById($user_id);
      $pass = $_POST['new_pass'];
      if(password_verify($_POST['old_pass'], $usr['pass'])) {
        if(strlen($pass) > 4 ) {
          if($pass===$_POST['new_pass2']) {
            if(user::updatePassword($user_id, $pass)) {
              \view::alert('success',__('_changes_updated'));
            }
          } else {
            \view::alert('alert',__('New passwords do not match'));
          }
        } else {
          \view::alert('alert',__('New password too small'));
        }
      } else {
        \view::alert('alert',__('Password incorrect'));
      }
    }

    if (\router::post('token')=='generate') {
      $token = self::generateToken();
      while(count(user::getIdsByMeta('token', $token)) > 0) {
        $token = self::generateToken();
      }
      user::meta($user_id, 'token', $token);
      \view::alert('success',__('_changes_updated'));
    }

    if (\router::post('token')=='delete') {
      user::meta($user_id, 'token', '');
      \view::alert('success',__('_changes_updated'));
    }
  }

  static function generateToken() {
    $token = '';
    while(strlen($token) < 160) {
      $token .= hash('sha512', uniqid(true));
    }
    return substr($token, 0, 160);
  }

}
