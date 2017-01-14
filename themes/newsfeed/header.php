<!DOCTYPE html>
<html>
<head>
<base href="<?=gila::config('base')?>">
<title><?=gila::config('title')?></title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="libs/bootstrap/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="libs/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="themes/newsfeed/assets/css/animate.css">
<!--link rel="stylesheet" type="text/css" href="themes/newsfeed/assets/css/font.css"-->
<link rel="stylesheet" type="text/css" href="themes/newsfeed/assets/css/li-scroller.css">
<link rel="stylesheet" type="text/css" href="themes/newsfeed/assets/css/slick.css">
<link rel="stylesheet" type="text/css" href="themes/newsfeed/assets/css/jquery.fancybox.css">
<link rel="stylesheet" type="text/css" href="themes/newsfeed/assets/css/theme.css">
<link rel="stylesheet" type="text/css" href="themes/newsfeed/assets/css/style.css">
<!--[if lt IE 9]>
<script src="themes/newsfeed/assets/js/html5shiv.min.js"></script>
<script src="themes/newsfeed/assets/js/respond.min.js"></script>
<![endif]-->
</head>
<body>
<div id="preloader">
  <div id="status">&nbsp;</div>
</div>
<a class="scrollToTop" href="#"><i class="fa fa-angle-up"></i></a>
<div class="container">
  <header id="header">
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="header_top">
          <div class="header_top_left">
            <ul class="top_nav">
              <li><a href="index.html">Home</a></li>
              <li><a href="#">About</a></li>
              <li><a href="pages/contact.html">Contact</a></li>
            </ul>
          </div>
          <div class="header_top_right">
            <p>Friday, December 05, 2045</p>
          </div>
        </div>
      </div>
      <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="header_bottom">
          <div class="logo_area"><a href="index.html" class="logo"><img src="themes/newsfeed/images/logo.jpg" alt=""></a></div>
          <div class="add_banner"><a href="#"><img src="themes/newsfeed/images/addbanner_728x90_V1.jpg" alt=""></a></div>
        </div>
      </div>
    </div>
  </header>
  <section id="navArea">
    <nav class="navbar navbar-inverse" role="navigation">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
      </div>
      <div id="navbar" class="navbar-collapse collapse">
        <ul class="nav navbar-nav main_nav">
            <li class="active"><a href=""><span class="fa fa-home desktop-home"></span><span class="mobile-show">Home</span></a></li>
<?php
$mm = gila::menu();
//echo var_export($mm,true);
foreach ($mm as $mi) {
        if (!isset($mi['children'])) {
            echo "<li><a href=\"{$mi['url']}\">{$mi['title']}</a></li>";
        }
        else {
            echo "<li class=\"dropdown\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-expanded=\"false\">";
            echo "<a href=\"{$mi['url']}\" >{$mi['title']}</a>";
            echo "<ul class=\"dropdown-menu\" role=\"menu\">";
            foreach ($mi['children'] as $mii) {
                echo "<li><a href=\"{$mii['url']}\">{$mii['title']}</a></li>"; }
            echo "</ul></li>";
        }
} ?>
        </ul>
      </div>
    </nav>
  </section>
  <section id="newsSection">
    <div class="row">
      <div class="col-lg-12 col-md-12">
          <?php view::widget('latest-newsarea'); ?>
      </div>
    </div>
  </section>
