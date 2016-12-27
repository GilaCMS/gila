<?php

class model
{
    private $db;

    function __construct ()
    {
        $this->db = new db($GLOBALS['db']['host'], $GLOBALS['db']['user'], $GLOBALS['db']['pass'], $GLOBALS['db']['name']);
    }
}
