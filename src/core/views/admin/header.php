<!DOCTYPE html>
<html lang="<?=Gila::config('language')?>">

<head>
  <base href="<?=Gila::base_url()?>">
  <?php View::set('page_title',((Gila::config('title')??'Gila CMS').' - '.($page_title??__('Administration')))) ?>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width initial-scale=1">
  <link rel="icon" type="image/png" href="<?=Gila::config('admin_logo')?:'assets/gila-logo.png'?>">
  <link rel="stylesheet" href="lib/font-awesome/css/font-awesome.min.css">
  <?=View::css('lib/gila.min.css')?>
  <?=View::css('src/core/assets/admin/style.css')?>
  <?=View::script("lib/jquery/jquery-3.3.1.min.js")?>
  <?=View::script("lib/gila.min.js")?>
</head>

<body style="background:#f5f5f5">
<?php if(!isset($_COOKIE['sidebar_toggled'])) $_COOKIE['sidebar_toggled']=true ?>
  <div id="wrapper"<?=($_COOKIE['sidebar_toggled']=='true'? ' class="toggled"': '')?>>

    <!-- Sidebar g-nav vertical -->
    <div id="sidebar-wrapper">
      <div style="position: relative;height: 100px;">
        <a href="admin">
          <img style="max-width:180px;max-height:60px" src="<?=Gila::config('admin_logo')?:'assets/gila-logo.png'?>" class="centered">
        </a>
      </div>
      <ul class="g-nav vertical">
        <?php
          foreach (Gila::$amenu as $key => $value) {
            if(isset($value['access'])) if(!Gila::hasPrivilege($value['access'])) continue;
            if(isset($value['icon'])) $icon = 'fa-'.$value['icon']; else $icon='';
            echo "<li><a href='".Gila::url($value[1])."'><i class='fa {$icon}'></i> ".__("$value[0]")."</a>";
            if(isset($value['children'])) {
              echo "<ul class=\"dropdown\">";
              foreach ($value['children'] as $subkey => $subvalue) {
                if(isset($subvalue['access'])) if(!Gila::hasPrivilege($subvalue['access'])) continue;
                if(isset($subvalue['icon'])) $icon = 'fa-'.$subvalue['icon']; else $icon='';
                echo "<li><a href='".Gila::url($subvalue[1])."'><i class='fa {$icon}'></i> ".__("$subvalue[0]")."</a></li>";
              }
              echo "</ul>";
            }
            echo "</li>";
          }
        ?>
      </ul>
    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content -->
    <div class="g-group fullwidth bordered" style="vertical-align:baseline; background:white;">
      <a href="#menu-toggle" class="btn btn-white g-group-item" id="menu-toggle" title="Toggle Menu"><i class='fa fa-bars'></i></a>
      <a href="<?=Gila::base_url()?>" class="btn btn-white g-group-item" title="Homepage" target="_blank"><i class='fa fa-home'></i></a>

      <span class="g-group-item fullwidth text-align-right pad">
        <ul class="g-nav">
          <?php
          foreach(Gila::getList('badge') as $b) {
            echo "<li>{$b['icon']}<span class='badge' data-count='{$b['count']()}'></li>";
          }
          ?>
        <li>
          <i class="fa fa-user"></i> <?=Session::key('user_name')?> <i class="fa fa-angle-down"></i>
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
