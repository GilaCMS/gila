<?php

use core\models\user as user;

class login extends controller
{

  function __construct ()
  {
    gila::addLang('core/lang/login/');
  }

  function indexAction()
  {
    if(session::key('user_id')>0) {
       echo "<meta http-equiv='refresh' content='0;url=".gila::base_url()."' />";
       exit;
    }
    if(session::waitForLogin()>0) {
      view::alert('error', __('login_error_msg2'));
    } else if (isset($_POST['username']) && isset($_POST['password'])) {
      view::alert('error', __('login_error_msg'));
    }
    view::includeFile('login.php');
  }

  function callbackAction()
  {
    event::fire('login.callback');
  }

  function registerAction()
  {
    if(session::key('user_id')>0 || gila::config('user_register')!=1) {
       echo "<meta http-equiv='refresh' content='0;url=".gila::config('base')."' />";
       exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && event::get('recaptcha',true)) {
      $email = $_POST['email'];
      $password = $_POST['password'];
      $name = $_POST['name'];
      if(user::getByEmail($email)) {
        view::alert('error', __('register_error1'));
      }
      else {
        // register the user
        if(user::create($email,$password,$name)) {
          // success
          view::includeFile('login-register-success.php');
          return;
        } else {
          view::alert('error', __('register_error2'));
        }
      }
    }
    view::includeFile('register.php');
  }

  function password_resetAction()
  {
    if(isset($_GET['rp'])) {
      $r = user::getByResetCode($_GET['rp']);
      if (!$r) {
        echo  __('reset_error1');
          exit;
      }
      else if(isset($_POST['pass'])) {
        $idUser=$r[0];
        user::updatePassword($idUser,$_POST['pass']);
        view::includeFile('login-change-success.php');
        exit;
      } else {
        view::includeFile('login-change-new.php');
        exit;
      }
    }

    if(!isset($_POST['email'])) {
      view::includeFile('login-change-password.php');
      return;
    }

    $email = $_POST['email'];
    $out = [];

    $r = user::getByEmail($email);

    if ($r == false) {
      view::alert('error', __('reset_error2'));
      view::includeFile('login-change-password.php');
      return;
    }

    $baseurl = gila::base_url();
    $subject = __('reset_msg_ln1').' '.$r['username'];
    $reset_code = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'),0,50);
    $message = __('reset_msg_ln2')." {$r['username']}\n\n";
    $message .= __('reset_msg_ln3')." $baseurl\n\n";
    $message .= $baseurl."login/password_reset?rp=$reset_code\n\n";
    $message .= __('reset_msg_ln4');
    $headers = "From: GilaCMS <noreply@{$_SERVER['HTTP_HOST']}>";
    user::meta($r['id'],'reset_code',$reset_code);
    mail($email,$subject,$message,$headers);

    view::includeFile('login-change-emailed.php');
  }
}
