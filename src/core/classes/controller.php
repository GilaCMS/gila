<?php

class controller
{
    protected $view;

    function __construct()
    {
        $this->view = new view();
    }
}
