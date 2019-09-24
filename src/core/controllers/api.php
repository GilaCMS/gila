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
            trigger_error("Controller could not be found: $controller=>$controller_file");
            exit;
        }
        router::args_shift();

        require_once $controller_file;

        if(isset(gila::$controllerClass[$controller])) {
            $controller = gila::$controllerClass[$controller];
        }
        $ctrl = new $controller();

        $action = router::get('action',1);
        if (method_exists($controller,$action.'Action')) {
            $action_fn = $action.'Action';
            array_shift(router::$args);
        } else $action_fn = 'indexAction';

        $_REQUEST['g_response']='json';
        $ctrl->$action_fn();
    }

}
