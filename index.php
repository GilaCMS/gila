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
	//require_once 'config.default.php';
}


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

$db = new db($GLOBALS['db']['host'], $GLOBALS['db']['user'], $GLOBALS['db']['pass'], $GLOBALS['db']['name']);
new session();
new router();
/*
$log = new Monolog\Logger('name');
$log->pushHandler(new StreamHandler(__DIR__.'/log/warning2.log', Monolog\Logger::WARNING));
$log->pushHandler(new StreamHandler('log/error2.log', Monolog\Logger::ERROR));

// add records to the log
$log->addWarning('Foo3');
$log->addError('Bar3');*/
