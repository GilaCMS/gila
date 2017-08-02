<?php

//use core\controllers\blog as blog;

class rss extends controller
{
    public static $page;

    function __construct ()
    {
        self::$page=(router::get('page',1))?:1;
    }

    function indexAction()
    {
      $title=gila::config('title');
      $link=gila::config('base');
      $description=gila::config('slogan');
      include_once 'src/core/controllers/blog.php';
      $items=blog::latestposts();
      include 'src/core/views/rss.php';
    }

}
