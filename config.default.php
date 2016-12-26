<?php

/*
$GLOBALS['db'] = [
	'user' => "localhost",
	'user' => "root",
	'pass' => "",
	'name' => "gila"
];*/

$GLOBALS['default'] = [
	'controller' => "welcome",
	'admin/controller' => "dashboard",
];
$GLOBALS['path'] = [
	'base' => '//localhost/gila/',
	'controller' => [
		'welcome' => "core/controllers/welcome",
		'install' => "core/controllers/install",
	],
	'admin/controller' => [
		'dashboard' => "core/controllers/dashboard",
		'addons' => "core/controllers/addons",
		'settings' => "core/controllers/settings",
	],
	'theme' => [
		'default' => 'themes/andia/',
		'admin' => 'src/core/theme/'
	]
];
$GLOBALS['menu'] = array(
	'admin' => [
		['Dashboard','admin/dashoard','icon'=>'icon'],
		['Add-Ons','admin/addons','icon'=>'icon'],
		['Settings','admin/settings','icon'=>'icon']
	]
);
