<?php


class webhook extends Controller
{
    function __construct ()
    {
        $folder = LOG_PATH.'/webhooks/';
        if($id=Router::get('id',1)) $folder .= $id.'/';
        Gila::dir($folder);
        if($_POST != [] || $_POST = json_decode(file_get_contents("php://input"),true)){
            $ip = ($_SERVER['REMOTE_HOST'] ?? $_SERVER['REMOTE_ADDR']);
            file_put_contents($folder.date("Y-m-d H:i:s ").$ip.".json",
                json_encode($_POST,JSON_PRETTY_PRINT));
        }
    }

    function indexAction ()
    {

    }
}
