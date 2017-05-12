<?php

class router
{
    static $args = [];

    function __construct ()
    {

        if(isset($_GET['url'])) $args = explode("/", $_GET['url']); else $args = [];

        $controller = gila::config('default-controller');

        if (isset($args[0])) {
        	if(isset(gila::$controller[$args[0]])) {
        		$controller = $args[0];
        	} else {
        		array_splice($args, 0, 0, $controller);
        	}
        }
        else {
        	array_splice($args, 0, 0, $controller);
        }

        if (!isset(gila::$controller[$controller])) $controller = 'blog';

        $controller_file = 'src/'.gila::$controller[$controller].'.php';

        if(!file_exists($controller_file)) {
            trigger_error("Controller could not be found: $controller=>$controller_file", E_NOTICE);
            exit;
        }

        require_once $controller_file;
    	$ctrl = new $controller();
    	$action = 'index';

    	if (isset($args[1])) {
            if (method_exists($controller,$args[1].'Action') || method_exists($controller,$args[1].'Admin') || method_exists($controller,$args[1].'Ajax')) {
                $action = $args[1];
            } else array_splice($args, 1, 0, $action);
        }
    	else {
             array_splice($args, 1, 0, $action);
    	}

        if (method_exists($controller,$action.'Action')) {
            $action_fn = $action.'Action';
        }
        else if (method_exists($controller,$action.'Admin')) {
            if (session::user_id() == 0) {
                include __DIR__."/../views/login.phtml";
                exit;
            }
            $action_fn = $action.'Admin';
            $administration = 1;
        }
        else if (method_exists($controller,$action.'Ajax')) {
            router::$args = $args;
            $action_fn = $action.'Ajax';
            $ctrl->$action_fn();
            exit;
        }
        else {
            echo  $action." action not found!";
            exit;
        }

        router::$args = $args;
        $ctrl->$action_fn();
    }

    /*
    @key parameter
    @n place in url
    */
    static function get ($key, $n = null)
    {
        if (isset($_GET[$key])) {
            return $_GET[$key];
        }
        else if (isset(router::$args[$n+1])){
            if ($n == null) return null;
            return router::$args[$n+1];
        }
        else {
            return null;
        }
    }
    static function post ($key,$default=null)
    {
        return isset($_POST[$key])?$_POST[$key]:$default;
    }

    static function url ()
    {
        return $_GET['url'];
    }
    static function controller ()
    {
        return router::$args[0];
    }
    static function action ()
    {
        return router::$args[1];
    }
}
