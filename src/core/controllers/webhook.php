<?php


class webhook extends controller
{
    function __construct ()
    {
        if($_POST != []){
            file_put_contents("log/webhooks/".date("Y-m-d-H-i-s"),".json",
                json_encode($_POST,JSON_PRETTY_PRINT));
        }
    }

    function indexAction ()
    {

    }
}
