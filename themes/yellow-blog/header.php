<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">

<!--
---- Clean html template by http://WpFreeware.com
---- This is the main file (index.html).
---- You are allowed to change anything you like. Find out more Awesome Templates @ wpfreeware.com
---- Read License-readme.txt file to learn more.
-->

	<head>
		<base href="<?=gila::config('base')?>">
		<title><?=gila::config('title')?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<!--Oswald Font -->
		<link href='http://fonts.googleapis.com/css?family=Oswald:400,300,700' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" type="text/css" href="themes/yellow-blog/css/tooltipster.css" />
		<!-- home slider-->
		<link href="themes/yellow-blog/css/pgwslider.css" rel="stylesheet">
		<!-- Font Awesome -->
		<link rel="stylesheet" href="libs/font-awesome/css/font-awesome.min.css">
		<link href="themes/yellow-blog/style.css" rel="stylesheet" media="screen">
		<link href="themes/yellow-blog/responsive.css" rel="stylesheet" media="screen">
	</head>

	<body>

		<section id="header_area">
			<div class="wrapper header">
				<div class="clearfix header_top">
					<div class="clearfix logo floatleft">
<?php
$tt=explode(' ',$GLOBALS['config']['title']);
$tt2="";
for ($i=1;$i<count($tt);$i++) $tt2 .= $tt[$i];
?>
						<a href=""><h1><span><?=$tt[0]?></span> <?=$tt2?></h1></a>
					</div>
					<div class="clearfix search floatright">
						<form>
							<input type="text" placeholder="Search"/>
							<input type="submit" />
						</form>
					</div>
				</div>
				<div class="header_bottom">
					<?php view::widget('menu'); ?>
				</div>
			</div>
		</section>

		<section id="content_area">
