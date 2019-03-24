<?php
namespace core\models;

class profile
{

  static function postUpdate($user_id)
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') if (\router::post('submit-btn')=='submited'){
      user::updateName($user_id, strip_tags($_POST['gila_username']));
      user::meta($user_id, 'twitter_account', strip_tags($_POST['twitter_account']));
      \view::alert('success',__('_changes_updated'));
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') if (\router::post('submit-btn')=='password'){
      $usr = user::getById($user_id);
      $pass = $_POST['new_pass'];
      if(password_verify($_POST['old_pass'], $usr['pass'])) {
        if(strlen($pass) > 4 ) {
          if($pass===$_POST['new_pass2']) {
            user::updatePassword($user_id, $pass);
            \view::alert('success',__('_changes_updated'));
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
  }

}