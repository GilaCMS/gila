<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Cocur\Slugify\Slugify;

class welcome extends controller
{

  function indexAction ($args)
  {
      //echo "<pre>".var_export($_SERVER,ture)."</pre>";
      echo "//".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL']."<br>";
      echo "<br>Welcome to Gila CMS!<br>";
  }

  function testAction ($args)
  {
    $page = $args[1];

    $this->view->render("core/views/test.phtml");
  }

  function logAction ($args)
  {
      //set_include_path('core/'); // get_include_path().PATH_SEPARATOR.
      //spl_autoload_extensions('.php');
      //spl_autoload_register();

      // use mr\hla as hla;
/*      include 'hola.php';

      new mr\hla\hola();
      $d = new dog();*/

      //use Monolog\Logger;
      //use Monolog\Handler\StreamHandler;

      // create a log channel
      $log = new Logger('name');
      $log->pushHandler(new StreamHandler(__DIR__.'/../../../log/warning2.log', Logger::WARNING));
      $log->pushHandler(new StreamHandler(__DIR__.'/../../../log/error2.log', Logger::ERROR));
      $log->pushHandler(new StreamHandler(__DIR__.'/../../../log/error2.log', Logger::DEBUG));

      // add records to the log
     // $log->addWarning('Foo3');
      $log->addError('Bar3');
      $log->addDebug('Debug');
  }

  function slugAction ($args) {


      $slugify = new Slugify();

      echo $slugify->slugify('Γεια σου κοσμε, this is a long sentence and I need to make a slug from it!');
  }

}
