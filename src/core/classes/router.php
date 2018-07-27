<?php

class router
{
    static private $args = [];
    static $url;
    static $caching = false;
    static $caching_file;
    static private $controller;
    static private $action;

    function __construct ()
    {
        global $c;

        if(isset(gila::$route[$_GET['url']])) {
            gila::$route[$_GET['url']]();
            return;
        }

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

        router::$args = $args;
        if(isset(gila::$before[$controller][$action]))
            foreach(gila::$before[$controller][$action] as $fn) $fn();

        @call_user_func_array(array($c, $action_fn), $args);
        //$c->$action_fn();

        // end of response
        if(self::$caching) {
            $out2 = ob_get_contents();
            //ob_end_clean();
            $clog = new logger('log/cache.log');
            if(file_put_contents(self::$caching_file,$out2)){
                $clog->debug(self::$caching_file);
            }else{
                $clog->error(self::$caching_file);
            }
        }
    }


    static function get_controller (&$args):string
    {
        if(isset(self::$controller)) return self::$controller;
        $default = gila::config('default-controller');
        $controller = router::request('c',$default);

        if (isset($args[0])) {
            if(isset(gila::$controller[$args[0]])) {
                $controller = $args[0];
                array_shift($args);
            }
        }

        if ($controller==$default && !isset(gila::$controller[$default])) {
            // default-controller not found so have to reset on config.php file
            $controller = 'admin';
            gila::config('default-controller','admin');
            gila::updateConfigFile();
        }

        self::$controller = $controller;
        return $controller;
    }

    static function get_action(&$controller,&$args):string
    {
        global $c;
        if(isset(self::$action)) return self::$action;
        $action = self::request('action',@$args[0]?:'index');

        if(isset(gila::$action[$controller][$action])){
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

        if(isset($args[0]) && $args[0]==$action)
            array_shift($args);

        self::$action = $action;
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
        if ((isset(router::$args[$n-1])) && ($n != null) && (router::$args[$n-1]!=null)){
            return router::$args[$n-1];
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
        return @router::get_controller();
    }

    /**
    * Returns the name of the action
    */
    static function action ()
    {
        return @router::get_action();
    }

    static function args_shift()
    {
        array_shift(self::$args);
    }

    static function cache ($args = []) {
        //if(session::user_id()!=0) return;
        $dir = gila::dir('log/cache0/');
        self::$caching_file = $dir.str_replace(['/','\\'],'__',router::url()).http_build_query($args);
        if(file_exists(self::$caching_file) ) {
            if(sizeof($_REQUEST)>1) return;
            include self::$caching_file;
            exit;
        } else {
            ob_start();
            self::$caching = true;
        }
    }

}
