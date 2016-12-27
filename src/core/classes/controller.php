<?php

class controller
{
    protected $view;
    protected $db;

    function __construct()
    {
        $this->view = new view();
        $this->db = new db($GLOBALS['db']['host'], $GLOBALS['db']['user'], $GLOBALS['db']['pass'], $GLOBALS['db']['name']);
    }
}
