<?php

$configfile = __DIR__.'/../config.php';
$required_php = "7";
$required_ext = ['mysqli','zip','mysqlnd','json'];

if (file_exists($configfile)) {
    echo "<div class='alert'>config.php is already installed. You have to remove it before reinstalling the software</div>";
}else{
    include "requirements.php";
}
