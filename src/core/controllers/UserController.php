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
    Config::addLang('core/lang/login/');
    if (Session::key('user_id')>0) {
      $url = Config::get('user_redirect');
      $base = Config::base();
      echo "<meta http-equiv='refresh' content='0;url=".(!empty($url)?$url:$base)."' />";
      exit;
    }
    @header("X-Frame-Options: SAMEORIGIN");
    if (Session::waitForLogin()>0) {
      View::alert('error', __('login_error_msg2'));
    } elseif (isset($_POST['username']) && isset($_POST['password'])) {
      $usr = User::getByEmail($_POST['username']);
      $error = __('login_error_msg');
      if ($usr && $usr['active']==0 && password_verify($_POST['password'], $usr['pass'])) {
        $error =  __('login_error_inactive', $error);
      }
      View::alert('error', $error);
    }
    View::set('page_title', __('Log In'));
    View::includeFile('login.php');
  }

  public function callbackAction()
  {
    Event::fire('login.callback');
  }

  public function registerAction()
  {
    Config::addLang('core/lang/login/');
    if (Session::key('user_id')>0 || Config::get('user_register')!=1) {
      echo "<meta http-equiv='refresh' content='0;url=".Config::base('user')."' />";
      return;
    }
    View::set('page_title', __('Register'));

    if (Form::posted('register') && User::register($_POST)) {
      View::includeFile('user-register-success.php');
    } else {
      View::includeFile('register.php');
    }
  }

  public function activateAction()
  {
    if (Session::key('user_id')>0) {
      echo "<meta http-equiv='refresh' content='0;url=".Config::base('user')."' />";
      return;
    }

    if (isset($_GET['ap'])) {
      $ids = User::getIdsByMeta('activate_code', $_GET['ap']);
      if (!isset($ids[0])) {
        echo  __('activate_error1');
      } else {
        User::updateActive($ids[0], 1);
        User::metaDelete($ids[0], 'activate_code');
        View::set('user', User::getById($ids[0]));
        View::set('login_link', User::level($ids[0])>0? 'admin': 'user');
        View::includeFile('user-activate-success.php');
      }
      return;
    }
    http_response_code(400);
  }

  public function password_resetAction()
  {
    if (Session::key('user_id')>0) {
      echo "<meta http-equiv='refresh' content='0;url=".Config::base('user')."' />";
      return;
    }
    Config::addLang('core/lang/');
    Config::addLang('core/lang/login/');
    $rpa = 'reset-password-attempt';
    $rpt = 'reset-password-time';
    View::set('page_title', __('reset_pass'));

    if (isset($_GET['rp'])) {
      $r = User::getByResetCode($_GET['rp']);
      if (!$r) {
        echo  __('reset_error1');
      } elseif (Form::posted('new_pass') && isset($_POST['pass'])) {
        $idUser=$r[0];
        User::updatePassword($idUser, $_POST['pass']);
        User::metaDelete($idUser, 'reset_code');
        View::set('login_link', User::level($idUser)>0? 'admin': 'user');
        View::includeFile('user-change-success.php');
      } else {
        @session_start();
        $_SESSION['rpa'] = 0;
        @session_commit();
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
    @session_start();
    $_SESSION['rpa'] = $_SESSION['rpa'] ?? 0;
    $_SESSION['rpt'] = $_SESSION['rpt'] ?? time();

    if (Form::posted('reset_pass') && $r && $r['active']==1
      && ($_SESSION['rpa']<200 || $_SESSION['rpt']+3600<time())) {
      $_SESSION['rpa']++;
      $_SESSION['rpt'] = time();
      $reset_code = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 50);
      User::meta($r['id'], 'reset_code', $reset_code);
      $basereset = Config::base('user/password_reset');
      $reset_url = $basereset.'?rp='.$reset_code;

      if (!Event::get(
        'user_password_reset.email',
        false,
        ['user'=>$r, 'reset_code'=>$reset_code, 'reset_url'=>$reset_url]
      )) {
        $baseurl = Config::base();
        $subject = __('reset_msg_ln1').' '.$r['username'];
        $msg = __('reset_msg_ln2')." {$r['username']}\n\n";
        $msg .= __('reset_msg_ln3').' '.Config::get('title')."\n\n";
        $msg .= $reset_url."\n\n";
        $msg .= __('reset_msg_ln4');
        $headers = "From: ".Config::get('title')." <noreply@{$_SERVER['HTTP_HOST']}>";
        new Sendmail(['email'=>$email, 'subject'=>$subject, 'message'=>$msg, 'headers'=>$headers]);
      }
    }
    @session_commit();

    View::includeFile('user-change-emailed.php');
  }

  public function authAction()
  {
    header('Content-Type: application/json');
    if (!isset($_POST['email']) || !isset($_POST['password'])) {
      http_response_code(400);
      echo '{"success":false, "error":"'.__('login_error_msg').'"}';
      return;
    }
    $usr = User::getByEmail($_POST['email']);
    if ($usr && $usr['active']==1 && password_verify($_POST['password'], $usr['pass'])) {
      $token = '';
      $user_agent = $_SERVER['HTTP_USER_AGENT']??'';
      $ip = $_SERVER['REMOTE_ADDR']??'';
      while (strlen($token) < 60) {
        $token .= hash('sha512', uniqid(true));
      }
      $token = substr($token, 0, 60);
      Session::create($usr['id'], $token, $ip, $user_agent);
      Session::user($usr['id'], $usr['username'], $usr['email']);
      echo json_encode([
        'success'=>true,
        'id'=>$usr['id'],
        'username'=>$usr['username'],
        'token'=>$token
      ], JSON_UNESCAPED_UNICODE);
      return;
    } else {
      http_response_code(401);
      echo '{"success":false, "error":"'.__('login_error_msg').'"}';
    }
  }

  public function logoutAction()
  {
    if (Session::userId()===0) {
      http_response_code(403);
      return;
    }
    Session::destroy();
    if (Session::$token) {
      echo '{"success":true}';
    } else {
      echo "<meta http-equiv='refresh' content='0;url=".Config::get('base')."' />";
    }
  }

  public function uploadImageAction()
  {
    if (Session::userId()===0) {
      http_response_code(403);
      return;
    }
    if (isset($_FILES['uploadfiles'])) {
      if (isset($_FILES['uploadfiles']["error"])) {
        if ($_FILES['uploadfiles']["error"] > 0) {
          echo '{"success":false,"msg":"'.$_FILES['uploadfiles']['error'].'"}';
        }
      }

      $path = Config::dir(Config::get('umedia_path')??'assets/umedia/');
      $tmp_file = $_FILES['uploadfiles']['tmp_name'];
      $name = htmlentities($_FILES['uploadfiles']['name']);
      $ext = strtolower(pathinfo($name)['extension']);

      if (in_array($ext, ["jpg","jpeg","png","gif","webp"])) {
        do {
          $code = '';
          while (strlen($code) < 120) {
            $code .= hash('sha512', uniqid(true));
          }
          $target = $path.substr($code, 0, 120).'.'.$ext;
        } while (file_exists($target));

        if (!move_uploaded_file($tmp_file, $target)) {
          echo '{"success":false,"msg":"Could not upload the file"}';
          return;
        }
        $maxWidth = Config::get('maxImgWidth') ?? 0;
        $maxHeight = Config::get('maxImgHeight') ?? 0;
        if ($maxWidth>0 && $maxHeight>0) {
          Image::makeThumb($path, $path, $maxWidth, $maxHeight);
        }
        echo '{"success":true,"image":"'.htmlentities($target).'"}';
      } else {
        echo '{"success":false,"error":"Not a media file"}';
      }
    }
  }
}
