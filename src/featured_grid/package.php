<?php

$name='Featured Posts Grid';
$version='0.1';
$description='Adds a featured post grid comment section below every post in the front page.';
$author='Vasilis Zoumpourlis';
$url='gilacms.com';


$options = [
	'category' => [
		'type'=>'postcategory'
	],
	'height' => [
		'type'=>'select','options'=>['500px'=>'500px','400px'=>'400px']
	]
];
