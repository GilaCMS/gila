<?php

class router
{
    function __construct ()
    {
        //$uri = explode("?", $_SERVER["REQUEST_URI"]);
        //$uri = $_GET['url'];
        /*?><pre><?php echo var_export($_SERVER); ?></pre><br><?php
        echo $_GET['url']."<br>";*/
        if(isset($_GET['url'])) $args = explode("/", $_GET['url']); else $args = [];

        $controller = $GLOBALS['default']['controller'];
        $ctrl_path = $GLOBALS['path']["controller"];

        if (isset($args[0])) if ($args[0]=='admin') {
            $administration = 1;
            array_splice($args, 0, 1);
            $controller = $GLOBALS['default']['admin controller'];
            $ctrl_path = $GLOBALS['path']["admin controller"];
        }

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


            $path_theme = __DIR__.'/../../../themes/';
            if(isset($administration)) {
                $path_theme = $path_theme.$GLOBALS['path']['theme']['admin'];
            }
            else {
                $path_theme = $path_theme.$GLOBALS['path']['theme']['default'];
            }

            if (isset($ctrl->THEME)) {
                if ($ctrl->THEME == 0) {
                    echo "<base href='{$GLOBALS['path']['base']}'>";
                    $ctrl->$action_fn($args);
                    return;
                }
            }

            include $path_theme."/header.php";
            $ctrl->$action_fn($args);
            include $path_theme."/footer.php";

        }
    }

    private function admin()
    {

    }
}
