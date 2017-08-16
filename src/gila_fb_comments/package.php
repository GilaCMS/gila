<?php

$name='Facebook Comments Plugin';
$version='0.1';
$description='Adds a facebook comment section below every post. Needs application ID from FB. <br><b>Note: </b>This plugin will not work if gila is installed on local server.';
//$author='Author\'s Name';
$url='gilacms.com';
//$contact='';

$options = [
	'language' => [
		//'title'=>'Language',
		'type'=>'select',
		'default'=>'en_US',
		'options'=>['en_US'=>'English','es_ES'=>'Spanish','gr_GR'=>'Greek']
	],
	'appID' => [
		'title'=>'App ID',
		//'type'=>'text'
	]
];