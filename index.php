<?php
//use Monolog\Logger;
//use Monolog\Handler\StreamHandler;
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

//$GLOBALS['config']['db']['name'] = 'gila';

$db = new db(gila::config('db'));

gila::config('default-controller','blog');
gila::config('base', '//192.168.1.69/gila/');
gila::config('version', '1.0');
gila::config('theme', 'yellow-blog');
gila::controllers([
	'admin'=> 'core/controllers/admin',
	'blog'=> 'core/controllers/blog'
]);
/*$GLOBALS['config']['default-controller'] = "blog";
$GLOBALS['config']['path']['controller']['admin'] = "core/controllers/admin";
$GLOBALS['config']['path']['controller']['blog'] = "core/controllers/blog";
$GLOBALS['config']['base'] = '//192.168.1.69/gila/';
$GLOBALS['path']['base'] = '//192.168.1.69/gila/';
$GLOBALS['config']['version'] = '1.0';*/
$GLOBALS['menu'] = array(
	'admin' => [
		['Dashboard','admin','icon'=>'dashboard'],
		['Add-Ons','admin/addons','icon'=>'dropbox'],
		['Posts','admin/posts','icon'=>'pencil'],
		['Users','admin/users','icon'=>'users'],
		['Settings','admin/settings','icon'=>'cogs'],
		['Widgets','admin/widgets','icon'=>'th-large'],
	]
);
$GLOBALS['config']['packages'] = [];

$GLOBALS['config']['packages'] = $db->getArray("SELECT value FROM option WHERE option='package'");
foreach ($GLOBALS['config']['packages'] as $package) {
	include "src/$package/package.php";
}

new session();
new router();
/*
$log = new Monolog\Logger('name');
$log->pushHandler(new StreamHandler(__DIR__.'/log/warning2.log', Monolog\Logger::WARNING));
$log->pushHandler(new StreamHandler('log/error2.log', Monolog\Logger::ERROR));

// add records to the log
$log->addWarning('Foo3');
$log->addError('Bar3');*/
