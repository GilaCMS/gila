<?php

use core\models\User;

class login extends Controller
{

  function __construct ()
  {
    Gila::addLang('core/lang/login/');
  }

  function indexAction()
  {
    if(Session::key('user_id')>0) {
       echo "<meta http-equiv='refresh' content='0;url=".Gila::base_url()."' />";
       exit;
    }
    if(Session::waitForLogin()>0) {
      View::alert('error', __('login_error_msg2'));
    } else if (isset($_POST['username']) && isset($_POST['password'])) {
      View::alert('error', __('login_error_msg'));
    }
    View::includeFile('login.php');
  }

  function callbackAction()
  {
    Event::fire('login.callback');
  }

  function registerAction()
  {
    if(Session::key('user_id')>0 || Gila::config('user_register')!=1) {
       echo "<meta http-equiv='refresh' content='0;url=".Gila::config('base')."' />";
       exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && Event::get('recaptcha',true)) {
      $email = $_POST['email'];
      $password = $_POST['password'];
      $name = $_POST['name'];
      if(User::getByEmail($email)) {
        View::alert('error', __('register_error1'));
      }
      else {
        // register the user
        if(User::create($email,$password,$name)) {
          // success
          View::includeFile('login-register-success.php');
          return;
        } else {
          View::alert('error', __('register_error2'));
        }
      }
    }
    View::includeFile('register.php');
  }

  function password_resetAction()
  {
    $rpa = 'reset-password-attempt';
    $rpt = 'reset-password-time';

    if(isset($_GET['rp'])) {
      $r = User::getByResetCode($_GET['rp']);
      if (!$r) {
        echo  __('reset_error1');
      }
      else if(isset($_POST['pass'])) {
        $idUser=$r[0];
        User::updatePassword($idUser,$_POST['pass']);
        View::includeFile('login-change-success.php');
      } else {
        Session::key($rpa, 0);
        View::includeFile('login-change-new.php');
      }
      exit;
    }

    if(!isset($_POST['email'])) {
      View::includeFile('login-change-password.php');
      return;
    }

    $email = $_POST['email'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      View::alert('error', __('reset_error2'));
      View::includeFile('login-change-password.php');
      return;
    }

    $r = User::getByEmail($email);
    Session::define([$rpa=>0,$rpt=>time()]);
    $tries = (int)Session::key($rpa);
    $lastTime = (int)Session::key($rpt);

    if ($r && ($tries<2 || $lastTime+3600<time())) {
      Session::key($rpa, $tries+1);
      Session::key($rpt, time());

      $baseurl = Gila::base_url();
      $subject = __('reset_msg_ln1').' '.$r['username'];
      $reset_code = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'),0,50);
      $msg = __('reset_msg_ln2')." {$r['username']}\n\n";
      $msg .= __('reset_msg_ln3')." $baseurl\n\n";
      $msg .= $baseurl."login/password_reset?rp=$reset_code\n\n";
      $msg .= __('reset_msg_ln4');
      $headers = "From: GilaCMS <noreply@{$_SERVER['HTTP_HOST']}>";
      User::meta($r['id'],'reset_code',$reset_code);
      new sendmail(['email'=>$email, 'subject'=>$subject, 'message'=>$msg, 'headers'=>$headers]);
    }

    View::includeFile('login-change-emailed.php');
  }

  function authAction()
  {
    header('Content-Type: application/json');
    if(!isset($_POST['email']) || !isset($_POST['password'])) {
      http_response_code(400);
      echo '{"error":"Credencials missing"}';
      return;
    }
    $usr = User::getByEmail($_POST['email']);
    if ($usr && $usr['active']==1 && password_verify($_POST['password'], $usr['pass'])) {
      $token = core\models\User::meta($usr['id'], 'token');
      if($token) {
        echo '{"token":"'.$token.'"}';
        return;
      }
    }
    http_response_code(401);
    echo '{"error":"Credencials are not valid"}';
  }
}
