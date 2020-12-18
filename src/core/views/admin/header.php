<!DOCTYPE html>
<html lang="<?=Config::get('language')?>">

<head>
  <base href="<?=Config::base()?>">
  <title><?=((Config::get('title')??'Gila CMS').' - '.($page_title??__('Administration')))?></title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <meta name="viewport" content="width=device-width initial-scale=1">
  <link rel="icon" type="image/png" href="<?=View::thumb(Config::get('admin_logo')??'assets/gila-logo.png', 200)?>">
  <?php View::$stylesheet=[]?>
  <?=View::css('core/gila.min.css')?>
  <?=View::css('lib/font-awesome/css/font-awesome.min.css')?>
  <?=View::css('core/admin/style.css')?>
  <?=View::script("core/gila.min.js")?>
  <style>
  <?=(Config::get('admin_background')? 'background:url("'.Config::get('admin_background').'")': '')?>
  .widget-area-dashboard .widget{background:rgba(255,255,255,0.9)}  
  <?=file_get_contents(View::getAdminThemePath())??''?>
<?php if (!isset($_COOKIE['sidebar_toggled'])) {
  $_COOKIE['sidebar_toggled']='true';
}
if ($palette=Config::get('admin_palette')) {
  $p = json_decode($palette, true);
  echo ':root{';
  foreach ($p as $k=>$c) {
    echo '--main-palette-'.$k.':'.$c.';';
  }
  echo '--main-a-color:'.$p[0].';';
  echo '--main-primary-color:'.$p[0].';';
  echo '}';
}
?>
  </style>
</head>

<body>
  <div id="wrapper"<?=($_COOKIE['sidebar_toggled']=='true'? ' class="toggled"': '')?>>
    <!-- Sidebar g-nav vertical -->
    <div id="sidebar-wrapper"><div>
      <div style="position: relative;height: 100px;">
        <a href="admin">
          <img style="max-width:180px;max-height:60px" src="<?=View::thumb(Config::get('admin_logo')??'assets/gila-logo.png')?>" class="centered">
        </a>
      </div>
      <ul class="g-nav vertical lazy" data-load="lzld/amenu/?base=<?=Config::url('')?>">
      ...
      </ul>
    </div></div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content -->
    <div id="top-wrapper" class="g-group fullwidth">
      &nbsp;<a href="#menu-toggle" class="g-icon-btn g-group-item" id="menu-toggle" title="Toggle Menu"><i class='fa fa-bars'></i></a>
      <?php if ('admin'!=Config::get('default-controller')) {?>
      &nbsp;<a href="<?=Config::base()?>" class="g-icon-btn g-group-item" title="Homepage" target="_blank"><i class='fa fa-home'></i></a>
      <?php } ?>

      <span class="g-group-item fullwidth text-align-right" id="topbar">
        <ul class="g-nav g-navbar" style="background:unset">
          <?php
          foreach (Config::getList('badge') as $b) {
            echo "<li>{$b['icon']}<span class='badge' data-count='{$b['count']()}'></span></li>";
          }
          ?>
        <li style="color:unset" class="dropdown">
          <a href="javascript:void(0)">
            <i class="fa fa-user"></i> <?=Session::key('user_name')?>
          </a>
          <ul class="text-align-left dropdown-menu" style="right:0">
            <div class="g-screen" onclick="g('.dropdown').removeClass('open')"></div>
            <li style="position:sticky"><a href="admin/profile"><?=__("My Profile")?></a></li>
            <li style="position:sticky"><a href="admin/sessions"><?=__("Sessions")?></a></li>
            <li style="position:sticky"><a href="admin/logout"><?=__("Logout")?></a></li>
          </ul>
        </li>
        </ul>
      </span>
    </div>
    <div class="md-12" id="main-wrapper">

      <div class="wrapper" style="margin:10px">
