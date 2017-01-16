<?php

class router
{
    static $args = [];

    function __construct ()
    {
        //$uri = explode("?", $_SERVER["REQUEST_URI"]);
        //$uri = $_GET['url'];
        /*?><pre><?php echo var_export($_SERVER); ?></pre><br><?php
        //echo $_GET['url']."<br>";*/



        if(isset($_GET['url'])) $args = explode("/", $_GET['url']); else $args = [];

        $controller = gila::config('default-controller');
        $ctrl_path = gila::config('controller'); //$GLOBALS['config']['controller'];
        //$controller = $GLOBALS['config']['default-controller'];
        //$ctrl_path = $GLOBALS['config']['path']["controller"];
/*
        if (isset($args[0])) if ($args[0]=='admin') {
            $administration = 1;
            array_splice($args, 0, 1);
            $controller = $GLOBALS['default']['admin controller'];
            $ctrl_path = $GLOBALS['path']["admin controller"];
        }
*/
        if (isset($args[0])) {
        	if(isset($ctrl_path[$args[0]])) {
        		$controller = $args[0];
        	} else {
        		array_splice($args, 0, 0, $controller);
        	}
        }
        else {
        	array_splice($args, 0, 0, $controller);
        }

        $controller_file = 'src/'.$ctrl_path[$controller].'.php';

        if(!file_exists($controller_file)) {
        	echo $controller.' controller cannot be found!<br>'.$controller_file;
            exit;
        }
        else {
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


            //$path_theme = __DIR__.'/../../../themes/';
            //$path_theme = 'themes/';
            if(isset($administration)) {
                $path_theme = 'themes/admin';
            }
            else {
                $path_theme = 'themes/'.gila::config('theme');
            }
            /*
            if (isset($ctrl->THEME)) {
                if ($ctrl->THEME == 0) {
                    echo "<base href='{$GLOBALS['path']['base']}'>";
                    $ctrl->$action_fn();
                    return;
                }
            }
            */
            router::$args = $args;

            include $path_theme."/header.php";
            $ctrl->$action_fn();
            include $path_theme."/footer.php";

        }
    }
/*
@key
@n
*/
    static function get ($key, $n = null)
    {
        if (isset($_GET[$key])) {
            return $_GET[$key];
        }
        else if (isset(router::$args[$n+1])){
            return router::$args[$n+1];
        }
        else {
            return null;
        }
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
