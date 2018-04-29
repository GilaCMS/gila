<?php

class router
{
    static $args = [];
    static $url;

    function __construct ()
    {
        global $c;
        if(isset($_GET['url'])) {
            router::$url = strip_tags($_GET['url']);
            $args = explode("/", router::$url);
        }
        else {
            router::$url = false;
            $args = [];
        }

        $controller = router::get_controller($args);
        $controller_file = 'src/'.gila::$controller[$controller].'.php';

        if(!file_exists($controller_file)) {
            echo $controller_file;
            trigger_error("Controller could not be found: $controller=>$controller_file", E_NOTICE);
            exit;
        }

        if(isset(gila::$route[$_GET['url']])) {
            gila::$route[$_GET['url']]();
            return;
        }

        require_once $controller_file;
    	$c = new $controller();

        // find function to run after controller construction
        foreach(gila::$on_controller[$controller] as $fn) $fn();

    	$action = self::request('action','');
        if($action=='') $action='index';

    	if (isset($args[1])) {
            if(isset(gila::$action[$controller][$args[1]])){
                $action = $args[1];
                router::$args = $args;
                foreach(gila::$before[$controller][$action] as $fn) $fn();
                gila::$action[$controller][$action]();
                return;
            } else if (method_exists($controller,$args[1].'Action')) {
                $action = $args[1];
                $action_fn = $action.'Action';
            }
            else if (method_exists($controller,'indexAction')) {
                $action = 'index';
                $action_fn = $action.'Action';
            } else {
                array_splice($args, 1, 0, $action);
            }
        }
        else {
            if (method_exists($controller,'indexAction')) {
                $action = 'index';
                $action_fn = $action.'Action';
            } else {
                array_splice($args, 1, 0, $action);
            }
    	}

        if (!isset($action_fn)) {
            echo  $controller.' '.$action." action not found!";
            exit;
        }

        router::$args = $args;
        foreach(gila::$before[$controller][$action] as $fn) $fn();
        $c->$action_fn();
    }


    static function get_controller (&$args)
    {

        $controller = router::request('c',gila::config('default-controller'));

        if (isset($args[0])) {
            if(isset(gila::$controller[$args[0]])) {
                $controller = $args[0];
            } else if (file_exists('src/core/controllers/'.$args[0].'.php')) {
                $controller = $args[0];
                gila::$controller[$controller] = 'core/controllers/'.$controller;
            } else {
                array_splice($args, 0, 0, $controller);
            }
        }
        else {
            array_splice($args, 0, 0, $controller);
        }

        if (!isset(gila::$controller[$controller])) {
            // default-controller not found so have to reset on config.php file
            $controller = 'admin';
            gila::config('default-controller','admin');
            gila::updateConfigFile();
        }
        return $controller;
    }

    /**
    * Returns a get parameter value
    * @param $key (string) Parameter's name
    * @param $n optional (int) Parameter's expected position in a pretty url.
    * @return Parameter's value or null if paremeter is not found.
    */
    static function get ($key, $n = null)
    {
        if ((isset(router::$args[$n+1])) && ($n != null) && (router::$args[$n+1]!=null)){
            //if ($n == null) return null;
            return router::$args[$n+1];
        }
        else if (isset($_GET[$key])) {
            return $_GET[$key];
        }
        else if (isset($_GET['var'.$n])) {
            return $_GET['var'.$n];
        }
        else {
            return null;
        }
    }

    /**
    * Returns the value of a post parameter
    * @param $key (string) Parameter's name
    * @return null if the parameter is not set
    */
    static function post ($key,$default=null)
    {
        return isset($_POST[$key])?$_POST[$key]:$default;
    }

    static function request ($key,$default=null)
    {
        return isset($_REQUEST[$key])?$_REQUEST[$key]:$default;
    }

    static function url ()
    {
        return $_GET['url'];
    }

    /**
    * Returns the name of the controller
    */
    static function controller ()
    {
        return router::$args[0];
    }

    /**
    * Returns the name of the action
    */
    static function action ()
    {
        return router::$args[1];
    }
}
