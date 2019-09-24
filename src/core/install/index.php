<?php

if(isset($_GET['step'])) {
    if($_GET['step']==1) {
        include __DIR__.'/install.php';
        exit;
    }
}

$configfile = CONFIG_PHP;
$required_php = "7";
$required_ext = ['mysqli','zip','mysqlnd','json','gd','mbstring'];

if (file_exists($configfile)) {
    echo "<div class='alert'>config.php is already installed. You have to remove it before reinstalling the software</div>";
}else{
    include __DIR__."/requirements.php";
}
