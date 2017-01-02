<?php


$GLOBALS['config'] = [
	'title' => "Gila CMS",
	'slogan' => "An awesome website!",
	'base' => '//localhost/gila/',
];
$GLOBALS['default'] = [
	'controller' => "welcome",
];
$GLOBALS['path'] = [
	'base' => '//localhost/gila/',
	'controller' => [
		'welcome' => "core/controllers/welcome"
	],
	'theme' => [
		'default' => 'andia',
		'admin' => 'admin'
	]
];
$GLOBALS['menu'] = array(
	'admin' => [
		['Dashboard','admin/dashoard','icon'=>'icon'],
		['Add-Ons','admin/addons','icon'=>'icon'],
		['Posts','admin/posts','icon'=>'icon'],
		['Users','admin/users','icon'=>'icon'],
		['Settings','admin/settings','icon'=>'icon'],
	]
);
