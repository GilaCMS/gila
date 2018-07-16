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
            trigger_error("Controller could not be found: $controller=>$controller_file", E_NOTICE);
            exit;
        }

        if(isset(gila::$route[$_GET['url']])) {
            gila::$route[$_GET['url']]();
            return;
        }

        require_once $controller_file;

        if(isset(gila::$controllerClass[$controller])) {
            $controller = gila::$controllerClass[$controller];
        }
        $c = new $controller();

        // find function to run after controller construction
        if(isset(gila::$on_controller[$controller]))
            foreach(gila::$on_controller[$controller] as $fn) $fn();

        $action = router::get_action($controller,$args);
        $action_fn = $action.'Action';

        if($action=='index') if (!method_exists($controller,'indexAction')) {
            echo  $controller.' '.$action." action not found! wtf";
            exit;
        }

        router::$args = $args;
        if(isset(gila::$before[$controller][$action]))
            foreach(gila::$before[$controller][$action] as $fn) $fn();
        $c->$action_fn();
    }


    static function get_controller (&$args):string
    {
        $default = gila::config('default-controller');
        $controller = router::request('c',$default);

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

        if ($controller==$default && !isset(gila::$controller[$default])) {
            // default-controller not found so have to reset on config.php file
            $controller = 'admin';
            gila::config('default-controller','admin');
            gila::updateConfigFile();
        }

        return $controller;
    }

    static function get_action(&$controller,&$args):string
    {
        $action = self::request('action',@$args[1]?:'index');

        if(isset(gila::$action[$controller][$action])){
            //$action = $args[1];
            $aa = $action.'Action';
            @$c->$aa = gila::$action[$controller][$action];
        } else if (!method_exists($controller,$action.'Action')) {
            if (method_exists($controller,'indexAction')) {
                $action = 'index';
            } else {
                trigger_error("Controller $controller should have a indexAction() method", E_NOTICE);
                exit;
            }
        }

        if(!isset($args[1]) || $args[1]!=$action)
            array_splice($args, 1, 0, $action);
        return $action;
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
        return @router::$args[0];
    }

    /**
    * Returns the name of the action
    */
    static function action ()
    {
        return @router::$args[1];
    }
}
