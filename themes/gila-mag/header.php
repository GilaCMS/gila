<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?=gila::config('base')?>">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title><?=gila::config('base')?></title>
    <?php view::links(); ?>

    <style>
    <?php $theme_color=gila::option('theme.color','orangered'); ?>
    body{font-family:"Roboto","Helvetica Neue",Helvetica,Arial,sans-serif}
    .widget{padding: 0; margin-top: 12px; border: 1px solid #ccc;}
    .widget .g-nav.vertical li a{color: #181818;padding: 4px 12px}
    .widget .g-nav.vertical li a:hover{color: <?=$theme_color?>;}
    .widget-title{ background: <?=$theme_color?>; color: white;padding:8px}
    .post-review{border-bottom: 1px dashed #ccc;}
    .post-review a{color: #181818;}
    .post-review a:hover{color: <?=$theme_color?>;}
    .sidebar{border-left: 1px dashed #ccc;padding-left:8px; min-height:200px}
    .header{margin-bottom: 20px}
    .header h1{font-family:Arial;margin-left:8px}
    .featured-posts{margin-bottom: 20px}
    .header-logo{max-height: 80px; margin:10px}
    </style>
</head>

<body>
  <div  style="max-width:1000px; margin:auto">
    <div class="header">
        <?php view::widget_area('body'); ?>
        <div class="inline-block">
          <a href="<?=gila::config('base')?>" style="color:#333;">
          <?php
          $lgimg = gila::option('theme.header-logo');
          $_title = gila::config('title');
          echo ($lgimg?'<img class="header-logo" src="'.$lgimg.'" alt="'.$_title.'">':'<h1>'.$_title.'</h1>');
          ?>
          </a>
        </div>
        <!-- Navigation -->
        <nav class="inline-flex fullwidth g-navbar">
            <span class="fullwidth" style="border-top: 4px solid <?=$theme_color?>"><?php view::widget('menu'); ?></span>
      </nav>
    </div>
