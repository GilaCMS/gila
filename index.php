<?php

$GLOBALS['default'] = [
	'controller' => "welcome",
];
$GLOBALS['path'] = array(
	'app' => 'localhost/coan',
	'controller' => [
		'welcome' => "core/controllers/welcome"
	]
);


spl_autoload_register(function ($class) {
	require_once 'src/core/classes/'.$class.'.php';
});

new router();
