<?php

use Gila\User;
use Gila\Config;
use Gila\View;
use Gila\Event;
use Gila\Session;
use Gila\Sendmail;
use Gila\Router;

class UserController extends Gila\Controller
{
  public function __construct()
  {
    Config::addLang('core/lang/login/');
  }

  public function indexAction()
  {
    if (Session::key('user_id')>0) {
      echo "<meta http-equiv='refresh' content='0;url=".Config::base()."' />";
      exit;
    }
    if (Session::waitForLogin()>0) {
      View::alert('error', __('login_error_msg2'));
    } elseif (isset($_POST['username']) && isset($_POST['password'])) {
      View::alert('error', __('login_error_msg'));
    }
    View::set('title', __('Log In'));
    View::includeFile('login.php');
  }

  public function callbackAction()
  {
    Event::fire('login.callback');
  }

  public function registerAction()
  {
    if (Session::key('user_id')>0 || Config::get('user_register')!=1) {
      echo "<meta http-equiv='refresh' content='0;url=".Config::get('base')."' />";
      return;
    }
    View::set('title', __('Register'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && Event::get('recaptcha', true)) {
      $email = Router::request('email');
      $name = Router::request('name');
      $password = $_POST['password'];

      if ($name != $_POST['name']) {
        View::alert('error', __('register_error2'));
      } elseif (User::getByEmail($email) || $email != $_POST['email']) {
        View::alert('error', __('register_error1'));
      } else {
        // register the user
        $active = Config::get('user_activation')=='auto'? 1: 0;
        if ($user_Id = User::create($email, $password, $name, $active)) {
          // success
          if (Config::get('user_activation')=='byemail') {
            $baseurl = Config::base();
            $subject = __('activate_msg_ln1').' '.$r['username'];
            $activate_code = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 50);
            $msg = __('activate_msg_ln2')." {$r['username']}\n\n";
            $msg .= __('activatemsg_ln3')." $baseurl\n\n";
            $msg .= $baseurl."user/activate?ap=$activate_code\n\n";
            $msg .= __('reset_msg_ln4');
            $headers = "From: ".Config::get('title')." <noreply@{$_SERVER['HTTP_HOST']}>";
            User::meta($user_Id, 'activate_code', $activate_code);
            new Sendmail(['email'=>$email, 'subject'=>$subject, 'message'=>$msg, 'headers'=>$headers]);
          }
          View::includeFile('user-register-success.php');
          return;
        } else {
          View::alert('error', __('register_error2'));
        }
      }
    }
    View::includeFile('register.php');
  }

  public function activateAction()
  {
    if (Session::key('user_id')>0) {
      echo "<meta http-equiv='refresh' content='0;url=".Config::base()."' />";
      return;
    }

    if (isset($_GET['ap'])) {
      $ids = User::getIdsByMeta('activate_code', $_GET['ap']);
      if (!isset($ids[0])) {
        echo  __('activate_error1');
      } else {
        User::updateActive($ids[0], 1);
        User::metaDelete($ids[0], 'activate_code');
        View::includeFile('user-activate-success.php');
      }
      return;
    }
    http_response_code(400);
  }

  public function password_resetAction()
  {
    if (Session::key('user_id')>0) {
      echo "<meta http-equiv='refresh' content='0;url=".Config::base()."' />";
      return;
    }
    $rpa = 'reset-password-attempt';
    $rpt = 'reset-password-time';
    View::set('title', __('reset_pass'));

    if (isset($_GET['rp'])) {
      $r = User::getByResetCode($_GET['rp']);
      if (!$r) {
        echo  __('reset_error1');
      } elseif (isset($_POST['pass'])) {
        $idUser=$r[0];
        User::updatePassword($idUser, $_POST['pass']);
        View::includeFile('user-change-success.php');
      } else {
        Session::key($rpa, 0);
        View::includeFile('user-change-new.php');
      }
      exit;
    }

    if (!isset($_POST['email'])) {
      View::includeFile('user-change-password.php');
      return;
    }

    $email = $_POST['email'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      View::alert('error', __('reset_error2'));
      View::includeFile('user-change-password.php');
      return;
    }

    $r = User::getByEmail($email);
    Session::define([$rpa=>0,$rpt=>time()]);
    $tries = (int)Session::key($rpa);
    $lastTime = (int)Session::key($rpt);

    if ($r && ($tries<2 || $lastTime+3600<time())) {
      Session::key($rpa, $tries+1);
      Session::key($rpt, time());

      $baseurl = Config::base();
      $subject = __('reset_msg_ln1').' '.$r['username'];
      $reset_code = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 50);
      $msg = __('reset_msg_ln2')." {$r['username']}\n\n";
      $msg .= __('reset_msg_ln3')." $baseurl\n\n";
      $msg .= $baseurl."user/password_reset?rp=$reset_code\n\n";
      $msg .= __('reset_msg_ln4');
      $headers = "From: ".Config::get('title')." <noreply@{$_SERVER['HTTP_HOST']}>";
      User::meta($r['id'], 'reset_code', $reset_code);
      new Sendmail(['email'=>$email, 'subject'=>$subject, 'message'=>$msg, 'headers'=>$headers]);
    }

    View::includeFile('user-change-emailed.php');
  }

  public function authAction()
  {
    header('Content-Type: application/json');
    if (!isset($_POST['email']) || !isset($_POST['password'])) {
      http_response_code(400);
      echo '{"error":"Credencials missing"}';
      return;
    }
    $usr = User::getByEmail($_POST['email']);
    if ($usr && $usr['active']==1 && password_verify($_POST['password'], $usr['pass'])) {
      $token = User::meta($usr['id'], 'token');
      if ($token) {
        echo '{"token":"'.$token.'"}';
        return;
      }
    }
    http_response_code(401);
    echo '{"error":"Credencials are not valid"}';
  }

  // use this until user controller is created
  public function logoutAction()
  {
    global $db;
    User::metaDelete(Session::userId(), 'GSESSIONID', $_COOKIE['GSESSIONID']);
    Session::destroy();
    echo "<meta http-equiv='refresh' content='0;url=".Config::get('base')."' />";
  }
}
