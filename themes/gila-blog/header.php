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
    body{font-family:"Roboto","Helvetica Neue","Helvetica",Helvetica,Arial,sans-serif}
    .widget{padding: 0; margin-top: 12px; border: 1px solid #ccc;}
    .widget .g-nav.vertical li a{color: #181818;padding: 4px 12px}
    .widget .g-nav.vertical li a:hover{color: <?=$theme_color?>;}
    .widget-title{border-top: 4px solid <?=$theme_color?>; background: #181818; color: #ddd;padding:8px}
    .post-review{border-bottom: 1px dashed #ccc;}
    .post-review a{color: #181818;}
    .post-review a:hover{color: <?=$theme_color?>;}
    .sidebar{border-left: 1px dashed #ccc;padding-left:8px; min-height:200px}
    .header{margin-bottom: 20px}
    </style>
    <!--link href="lib/gila.min.css" rel="stylesheet"-->
</head>

<body>
  <div  style="max-width:1000px; margin:auto">
    <div class="header">
        <?php view::widget_area('body'); ?>
        <div class="inline-block">
          <h1><a href="<?=gila::config('base')?>" style="color:#333;font-family:Arial;margin-left:8px"><?=gila::config('title')?></a></h1>
        </div>
        <!-- Navigation -->
        <nav class="inline-flex fullwidth g-navbar">
            <span class="fullwidth" style="border-top: 4px solid <?=$theme_color?>"><?php view::widget('menu'); ?></span>
            <!--form method="get" class="inline-flex" action="<?=gila::make_url('blog')?>">
              <input name='search' class="g-input" value="<?=(isset($_GET['search'])?$_GET['search']:'')?>">
              <button class="g-btn" onclick='submit'>Search</button>
            </form-->
      </nav>
    </div>
