<?php

$configfile = __DIR__.'/../config.php';

if (file_exists($configfile)) {
    echo "<div class='alert'>config.php is already installed. You have to remove it before reinstalling the software</div>";
}
if($_SERVER['REQUEST_METHOD'] === 'POST') {
  $host=$_POST['db_host'];$db_user=$_POST['db_user'];
  $db_pass=$_POST['db_pass'];$db_name=$_POST['db_name'];
  $_base_url=$_POST['base_url'];

  $link = mysqli_connect($host,$db_user,$db_pass,$db_name);
  if (!$link) {
      echo "<div class='alert'><span class='closebtn' onclick='this.parentElement.style.display=\"none\";'>&times;</span>Error: Unable to connect to MySQL.".PHP_EOL;
      echo "<br>#".mysqli_connect_errno().PHP_EOL." : ".mysqli_connect_error().PHP_EOL."</div>";
  } else {
      include 'install.sql.php';

      // create config.php
      $filedata = file_get_contents(__DIR__.'/../config.default.php');
      $GLOBALS['config']['db'] = [
          'host' => $host,
          'user' => $db_user,
          'pass' => $db_pass,
          'name' => $db_name
      ];
      $GLOBALS['config']['packages'] = [];
      $GLOBALS['config']['base'] = $_base_url;
      $GLOBALS['config']['theme'] = 'base';
      $GLOBALS['config']['title'] = 'Gila CMS';
      $GLOBALS['config']['slogan'] = 'An awesome website!';
      $GLOBALS['config']['default-controller'] = 'blog';
      $GLOBALS['config']['timezone'] = 'America/Mexico_City';
      $GLOBALS['config']['ssl'] = '';
      $GLOBALS['config']['env'] = 'dev';

      $filedata = "<?php\n\n\$GLOBALS['config'] = ".var_export($GLOBALS['config'], true).";";

      file_put_contents($configfile, $filedata); //, FILE_APPEND | LOCK_EX

      include "installed.phtml";
      exit;
  }
}

include "install.phtml";
