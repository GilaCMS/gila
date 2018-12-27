<?php

$starttime = microtime(true);

if(!isset($_GET['url'])) $_GET['url'] = substr($_SERVER['REQUEST_URI'],1);

ini_set("error_log", "log/error.log");

spl_autoload_register(function ($class) {
	$class=str_replace('\\','/',$class);

	if (file_exists('src/core/classes/'.$class.'.php')) {
		require_once 'src/core/classes/'.$class.'.php';
	}
	else if (file_exists('src/'.$class.'.php')) {
		require_once 'src/'.$class.'.php';
	}
	else if (file_exists('lib/'.$class.'.php')) {
		require_once 'lib/'.$class.'.php';
	} else trigger_error("File $class could not be found with autoload.");
});

if (file_exists('config.php')) {
	require_once 'config.php';
}
else {
	if(isset($_GET['install'])) {
		include 'src/core/install/index.php';
	} else echo "Gila CMS is not installed.<meta http-equiv=\"refresh\" content=\"2;url=".gila::base_url()."?install\" />";
	exit;
}

$db = new db(gila::config('db'));


if ($GLOBALS['config']['env'] == 'dev') {
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	ini_set('display_startup_errors', '1');
	gila::load();
}
else {
	error_reporting(E_ERROR);
	ini_set('display_errors', 0);
	ini_set('display_startup_errors', 0);
	if(!include 'log/load.php') {
		gila::load();
		package::updateLoadFile();
	}
}

event::fire('load');
$g = new gila();


$theme = $GLOBALS['config']['theme'];
if(isset($_GET['g_preview_theme'])) $theme=$_GET['g_preview_theme'];
if(file_exists("themes/$theme/load.php")) include "themes/$theme/load.php";


new router();
