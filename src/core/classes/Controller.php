<?php

namespace Gila;

class Controller
{
  public static function admin()
  {
    if (Session::userId()===0) {
      Config::addLang('core/lang/login/');
      if (Session::waitForLogin()>0) {
        View::alert('error', __('login_error_msg2'));
      } elseif (isset($_POST['username']) && isset($_POST['password'])) {
        View::alert('error', __('login_error_msg'));
      }
      View::set('title', __('Log In'));
      View::renderFile('login.php');
      exit;
    }
    if(User::level(Session::userId())===0) {
      http_response_code(403);
      exit;
    }
  }

  public static function access($pri)
  {
    if (Session::hasPrivilege($pri)===false) {
      http_response_code(403);
      exit;
    }
  }

  public function __call($method, $args)
  {
    if (isset($this->$method)) {
      $func = $this->$method;
      return call_user_func_array($func, $args);
    }
  }
}
