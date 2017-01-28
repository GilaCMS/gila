<?php
/*!
 * Gila CMS
 * Copyright 2017 Vasileios Zoumpourlis
 * Licensed under MIT LICENSE
 */


$starttime = microtime(true);

if (file_exists(__DIR__.'/config.php')) {
	require_once 'config.php';
}
else {
	echo "Gila CMS is not installed.";
	exit;
}

ini_set("error_log", "log/error.log");

spl_autoload_register(function ($class) {

	if (file_exists('src/core/classes/'.$class.'.php')) {
		require_once 'src/core/classes/'.$class.'.php';
	}
	else {
		$class=str_replace('\\','/',$class);
		require_once 'libs/'.$class.'.php';
		//$log->warning();
	}
});



$db = new db(gila::config('db'));

$packages = $db->getArray("SELECT value FROM option WHERE option='package'");
foreach ($packages as $package) {
	include "src/$package/load.php";
}

new gila();
new session();
new router();
