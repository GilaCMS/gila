<?php

class welcome extends controller
{

  function indexAction ($args)
  {
    echo "Welcome to Gila CMS!";
  }

  function testAction ($args)
  {
    $page = $args[1];

    $this->view->display("a test view");
    //$this->view->display("core/views/test.phtml");
  }

  function dogAction ($args)
  {
      //set_include_path('core/'); // get_include_path().PATH_SEPARATOR.
      //spl_autoload_extensions('.php');
      //spl_autoload_register();

      // use mr\hla as hla;
      include 'hola.php';

      new mr\hla\hola();
      $d = new dog();
  }

}
