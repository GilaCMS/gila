<?php


class api extends Controller
{
    function __construct ()
    {
        if(Session::key('user_id')==0) {
            exit;
        }
    }

    function indexAction ()
    {
        $controller = Router::get('controller',1);

        if(isset(Gila::$controller[$controller])) {
            $controller_file = 'src/'.Gila::$controller[$controller].'.php';
        } else exit;

        if(!file_exists($controller_file)) {
            trigger_error("Controller could not be found: $controller=>$controller_file");
            exit;
        }
        Router::args_shift();

        require_once $controller_file;

        if(isset(Gila::$controllerClass[$controller])) {
            $controller = Gila::$controllerClass[$controller];
        }
        $ctrl = new $controller();

        $action = Router::get('action',1);
        if (method_exists($controller,$action.'Action')) {
            $action_fn = $action.'Action';
            array_shift(Router::$args);
        } else $action_fn = 'indexAction';

        $_REQUEST['g_response']='json';
        $ctrl->$action_fn();
    }

}
