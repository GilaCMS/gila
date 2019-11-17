<?php

class controller
{
  static function admin()
  {
    if(session::key('user_id')==0) {
      gila::addLang('core/lang/login/');
      if(session::waitForLogin()>0) {
        view::alert('error', __('login_error_msg2'));
      } else if (isset($_POST['username']) && isset($_POST['password'])) {
        view::alert('error', __('login_error_msg'));
      }
      view::renderFile('login.php');
      exit;
    }
  }

  static function access($pri)
  {
    if(gila::hasPrivilege($pri)===false) {
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
