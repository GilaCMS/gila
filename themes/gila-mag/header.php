<!DOCTYPE html>
<html lang="<?=gila::config('language')?>">
<?php
view::stylesheet('lib/gila.min.css');
view::head()?>
<style>
<?php $theme_color=gila::option('theme.color','orangered'); ?>
body{font-family:"Roboto","Helvetica Neue",Helvetica,Arial,sans-serif}
.widget{padding: 0; margin-top: 12px;}
.sidebar .widget{border: 1px solid #ccc;}
.widget .g-nav.vertical li a{color: #181818;padding: 4px 12px}
.widget .g-nav.vertical li a:hover{color: <?=$theme_color?>;}
.widget-title{ background: <?=$theme_color?>; color: white;padding:8px}
.footer-widget .widget{width:33%; display:inline-grid;min-width: 240px}
.post-review{border-bottom: 1px dashed #ccc;}
.post-review a{color: #181818;}
.post-review a:hover{color: <?=$theme_color?>;}
.sidebar{padding-left:8px; min-height:200px}
.header{margin-bottom: 20px}
.header h1{font-family:Arial;margin-left:8px}
.featured-posts{margin-bottom: 20px}
.header-logo{max-height: 80px; margin:10px}
footer{background:#464a49;margin-top:10px;color:white}
.footer-text,footer a,footer a:hover{color:#ccc; }
.widget-social-icons li a i:hover:before{background: <?=$theme_color?>;}
.g-navbar li ul li a{color:inherit}
.g-nav li ul{border-width:0; background: #181818; margin-top:-2px}
.g-nav .active{background: <?=$theme_color?>}
li.active{background-color:var(--main-primary-color); color:white;}
</style>

<body>
  <div  style="max-width:1100px; margin:auto">
    <div class="header">
        <?php view::widget_area('body'); ?>
        <div class="inline-block">
          <a href="<?=gila::base_url()?>" style="color:#333;">
          <?php
          $lgimg = gila::option('theme.header-logo');
          $_title = gila::config('title');
          echo ($lgimg?'<img class="header-logo" src="'.$lgimg.'" alt="'.$_title.'">':'<h1>'.$_title.'</h1>');
          ?>
          </a>
        </div>
        <!-- Navigation -->
        <nav class="inline-flex fullwidth g-navbar">
            <span class="fullwidth" style="border-top: 4px solid <?=$theme_color?>"><?php view::menu(); ?></span>
      </nav>
    </div>
    <div class="wrapper">
