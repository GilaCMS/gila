<?php

$configfile = __DIR__.'/../config.php';

if (file_exists($configfile)) {
    echo "<div class='alert'>config.php is already installed. You have to remove it before reinstalling the software</div>";
    //exit;
}
if($_SERVER['REQUEST_METHOD'] === 'POST') {
  $host=$_POST['db_host'];$db_user=$_POST['db_user'];
  $db_pass=$_POST['db_pass'];$db_name=$_POST['db_name'];

  $link = mysqli_connect($host,$db_user,$db_pass,$db_name);
  if (!$link) {
      echo "<div class='alert'><span class='closebtn' onclick='this.parentElement.style.display=\"none\";'>&times;</span>Error: Unable to connect to MySQL.".PHP_EOL;
      echo "<br>#".mysqli_connect_errno().PHP_EOL." : ".mysqli_connect_error().PHP_EOL."</div>";
  } else {
      $link->query('CREATE TABLE IF NOT EXISTS user(id int(11) NOT NULL auto_increment,name varchar(80),email varchar(80),pass varchar(60),reset_code varchar(60),PRIMARY KEY (id));');
      $link->query('CREATE TABLE IF NOT EXISTS usermeta(id int(11) NOT NULL auto_increment,user_id int(11),vartype varchar(80),value varchar(80),PRIMARY KEY (id));');
      $link->query('CREATE TABLE IF NOT EXISTS post(id int(11) NOT NULL auto_increment,user_id int(11),title varchar(80),slug varchar(80),post TEXT,publish int(1),updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (id));');
      $link->query('CREATE TABLE IF NOT EXISTS postmeta(id int(11) NOT NULL auto_increment,post_id int(11),vartype varchar(25),value varchar(80),PRIMARY KEY (id));');
      $link->query('CREATE TABLE IF NOT EXISTS postcategory(id int(11) NOT NULL auto_increment,name varchar(80),PRIMARY KEY (id));');
      $link->query('CREATE TABLE IF NOT EXISTS page(id int(11) NOT NULL auto_increment,title varchar(80),slug varchar(80),page TEXT,publish int(1),updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (id));');
      $link->query('CREATE TABLE IF NOT EXISTS option(id int(11) NOT NULL auto_increment,option varchar(80),value varchar(80),PRIMARY KEY (id));');
      $link->query('CREATE TABLE IF NOT EXISTS widget(id int(11) NOT NULL auto_increment,widget varchar(80),area varchar(80),pos int(2),data TEXT,PRIMARY KEY (id));');

      $_user=$_POST['adm_user'];
      $_email=$_POST['adm_email'];
      $_pass=$_POST['adm_pass'];
      $_base_url=$_POST['base_url'];

      $link->query("INSERT INTO user VALUES(1,'$_user','$_email','$_pass','');");
      $link->query("INSERT INTO usermeta VALUES(1,1,'privilege','admin');");
      $link->query("INSERT INTO post VALUES(1,1,'Hello World','hello_world','This is the first post',1,CURRENT_TIMESTAMP);");
      $link->query("INSERT INTO page VALUES(1,'About','about','This is a page to describe your website',1,CURRENT_TIMESTAMP);");
      $link->query("INSERT INTO widget VALUES(1,'menu','','','[{\"title\":\"Home\",\"url\":\"\"},{\"title\":\"Page\",\"url\":\"about\"}]');");

      echo "<div class='alert success'>Installation executed successfully!</div>";

      // create config.php
      $filedata = file_get_contents(__DIR__.'/../config.default.php');
      $GLOBALS['config']['db'] = [
          'host' => $host, //localhost
          'user' => $db_user, // root
          'pass' => $db_pass, //
          'name' => $db_name //
      ];
      $GLOBALS['config']['packages'] = [];
      $GLOBALS['config']['base'] = $_base_url;
      /*$globkeys = ['db']; //,'default','path','menu'
      foreach ($globkeys as $key) {
          $filedata .= "\n\$GLOBALS['config']['$key'] = ".(var_export($GLOBALS['config'][$key], true)).";";
      }*/

      //foreach ($config_list as $key => $value) $GLOBALS['config'][$key] = $_POST['gila_'.$key];
      $GLOBALS['config']['theme'] = 'base';
      $GLOBALS['config']['title'] = 'Gila CMS';
      $GLOBALS['config']['slogan'] = 'An awesome website!';
      $GLOBALS['config']['default-controller'] = 'blog';
      $GLOBALS['config']['timezone'] = 'America/Mexico_City';
      $GLOBALS['config']['ssl'] = '';
      $GLOBALS['config']['env'] = 'dev';

      $filedata = "<?php\n\n\$GLOBALS['config'] = ".var_export($GLOBALS['config'], true).";";

      file_put_contents($configfile, $filedata); //, FILE_APPEND | LOCK_EX

      echo "Go to the website or login to admin panel";
      exit;
  }
}

include "install.phtml";
