<?php

class router
{
    function __construct ()
    {
        //$uri = explode("?", $_SERVER["REQUEST_URI"]);
        //$uri = $_GET['url'];
        /*?><pre><?php echo var_export($_SERVER); ?></pre><br><?php
        echo $_GET['url']."<br>";*/
        $args = explode("/", $_GET['url']);

        $controller = $GLOBALS['default']['controller'];

        if (isset($args[0])) if ($args[0]=='admin') {
            $administration = 1;
            array_splice($args, 0, 1);
        }

        if (isset($args[0])) {
        	if(isset($GLOBALS['path']["controller"][$args[0]])) {
        		$controller = $args[0];
        	} else {
        		array_splice($args, 0, 0, $controller);
        	}
        }
        else {
        	array_splice($args, 0, 0, $controller);
        }

        $controller_file = 'src/'.$GLOBALS['path']["controller"][$controller].'.php';

        if(!file_exists($controller_file)) {
        	echo $controller.' controller cannot be found!<br>'.$controller_file;
        }
        else {
        	require_once $controller_file;
        	$ctrl = new $controller();

        	$action = 'index';
        	if (isset($args[1])) {
        		if (method_exists($controller,$args[1].'Action')) {
                    $action = $args[1];
        		}
        		else {
                     array_splice($args, 0, 0, $action);
        		}
        	}
        	else {
                 array_splice($args, 0, 0, $action);
        	}
            $action_fn = $action.'Action';



            if(isset($administration)) {
                include __DIR__."/../theme/header.php";
                $ctrl->$action_fn($args);
                include __DIR__."/../theme/footer.php";
            }
            else {
                include __DIR__."/../../../themes/andia/header.php";
                $ctrl->$action_fn($args);
                include __DIR__."/../../../themes/andia/footer.php";
            }

        }
    }

    private function admin()
    {

    }
}
