<?php


class api extends controller
{
    function __construct ()
    {
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

}
