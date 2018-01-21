<?php


class api extends controller
{
    function __construct ()
    {
        if(session::key('user_id')==0) {
            exit;
        }
    }

    function indexAction ()
    {
        $controller = router::get('controller',1);

        if(isset(gila::$controller[$controller])) {
            $controller_file = 'src/'.gila::$controller[$controller].'.php';
        } else exit;

        if(!file_exists($controller_file)) {
            echo $controller_file;
            trigger_error("Controller could not be found: $controller=>$controller_file", E_NOTICE);
            exit;
        }
        array_shift(router::$args);

        require_once $controller_file;
        $ctrl = new $controller();

        $action = router::get('action',1);
        if (method_exists($controller,$action.'Action')) {
            $action_fn = $action.'Action';
            array_shift(router::$args);
        } else $action_fn = 'indexAction';

        $_REQUEST['g_response']='json';
        $ctrl->$action_fn();
    }

    function contentAction()
    {
        global $db,$table;
        $_key = router::get('t',1);

        if(isset(gila::$content[$_key])) {
            $_path = 'src/'.gila::$content[$_key];
            if(file_exists($_path)) {
                include $_path;
                switch ($_SERVER['REQUEST_METHOD']) {
                    case 'GET':
                        echo '<pre>'.json_encode($table,JSON_PRETTY_PRINT).'</pre>';
                        break;
                    case 'POST':
                        break;
                    case 'PUT':
                        break;
                    case 'DELETE':
                        break;
                }
            }
            else {
                echo $table." path is not found.";
            }
        }
    }

    function delete()
    {
        global $db,$table;
        echo $_POST['id'];
        $db->query("DELETE FROM ".$table['name']." WHERE ".$table['id']."=".$_POST['id'].";");
    }
}
