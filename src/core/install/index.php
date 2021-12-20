<?php

header('HTTP/1.1 503 Service Temporarily Unavailable');
header('Retry-After: 86400');

if (!isset($_GET['install'])) {
  echo "Gila CMS is not installed.<meta name=\"robots\" content=\"noindex\">";
  echo "<meta http-equiv=\"refresh\" content=\"2;url=".Config::base()."?install\" />";
  exit;
}

if (isset($_GET['step'])) {
  if ($_GET['step']==1) {
    include __DIR__.'/install.php';
    exit;
  }
}

$configfile = CONFIG_PHP;
$required_php = "8";
$required_ext = ['mysqli','zip','mysqlnd','json','gd','mbstring'];

if (file_exists($configfile)) {
  echo "<div class='alert'>config.php is already installed. You have to remove it before reinstalling the software</div>";
} else {
  include __DIR__."/requirements.php";
}
