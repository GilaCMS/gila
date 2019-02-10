<?php

class controller
{
    static function admin()
    {
        if(session::key('user_id')==0) {
            gila::addLang('core/lang/login/');
            view::renderFile('login.php');
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
