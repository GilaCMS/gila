<!DOCTYPE html>
<html lang="<?=Gila::config('language')?>">

<head>
  <base href="<?=Gila::base_url()?>">
  <title><?=((Gila::config('title')??'Gila CMS').' - '.($page_title??__('Administration')))?></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width initial-scale=1">
  <link rel="icon" type="image/png" href="<?=Gila::config('admin_logo')?:'assets/gila-logo.png'?>">
  <?=View::css('core/gila.min.css')?>
  <?=View::css('lib/font-awesome/css/font-awesome.min.css')?>
  <?=View::css('core/admin/style.css')?>
  <?=View::script("lib/jquery/jquery-3.3.1.min.js")?>
  <?=View::script("core/gila.min.js")?>
  <style>#topbar .g-navbar>li>a{color:#222}#topbar .g-navbar>li>a:hover{color:#ddd}</style>
</head>

<?php if(!isset($_COOKIE['sidebar_toggled'])) $_COOKIE['sidebar_toggled']='true' ?>

<body style="background:#f5f5f5">
  <div id="wrapper"<?=($_COOKIE['sidebar_toggled']=='true'? ' class="toggled"': '')?>>
    <!-- Sidebar g-nav vertical -->
    <div id="sidebar-wrapper">
      <div style="position: relative;height: 100px;">
        <a href="admin">
          <img style="max-width:180px;max-height:60px" src="<?=Gila::config('admin_logo')?:'assets/gila-logo.png'?>" class="centered">
        </a>
      </div>
      <ul class="g-nav vertical lazy" data-load="lzld/amenu">
      ...
      </ul>
    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content -->
    <div class="g-group fullwidth bordered" style="vertical-align:baseline; background:white;">
      <a href="#menu-toggle" class="btn btn-white g-group-item" id="menu-toggle" title="Toggle Menu"><i class='fa fa-bars'></i></a>
      <a href="<?=Gila::base_url()?>" class="btn btn-white g-group-item" title="Homepage" target="_blank"><i class='fa fa-home'></i></a>

      <span class="g-group-item fullwidth text-align-right" id="topbar">
        <ul class="g-nav g-navbar" style="background:unset">
          <?php
          foreach(Gila::getList('badge') as $b) {
            echo "<li>{$b['icon']}<span class='badge' data-count='{$b['count']()}'></span></li>";
          }
          ?>
        <li style="color:unset">
          <a href="#">
            <i class="fa fa-user"></i> <?=Session::key('user_name')?> <i class="fa fa-angle-down"></i>
          </a>
          <ul class="text-align-left" style="right:0">
            <li><a href="admin/profile"><?=__("My Profile")?></a></li>
            <li><a href="admin/logout"><?=__("Logout")?></a></li>
          </ul>
        </li>
        </ul>
      </span>
    </div>
    <div class="md-12">

      <div class="wrapper bordered" style="background:white;margin:10px" id='main-wrapper'>
