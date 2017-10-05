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
    body{font-family:'Arial', sans-serif;}
    h1,h2,h3,.widget-title,.header{font-family:'Saira Extra Condensed', sans-serif;}
    .widget-title,.header{font-size:1.1em}
    /* "Yanone Kaffeesatz", "Microsoft YaHei Light", Arial, Helvetica, sans-serif
    body{font-family:-apple-system,"Helvetica",Helvetica,Arial,sans-serif}*/
    .widget{margin-top: 20px; padding: 0 8px}
    .widget-title{border-bottom: 4px solid <?=$theme_color?>; display:inline-block;}
    .widget-body{border-top: 1px solid #ccc}
    .widget:before{content: ""; width:100%; margin-top: 12px; border-bottom: 1px solid #ccc;}
    .widget .g-nav.vertical li{border-bottom: 1px solid #ddd}
    .widget .g-nav.vertical li a{color: #181818;padding: 4px 12px}
    .widget .g-nav.vertical li a:hover{color: <?=$theme_color?>;}
    /*.post-review{border-bottom: 1px dashed #ccc;}*/
    .post-review a{color: #181818;}
    .post-review a:hover{color: <?=$theme_color?>;}
    .sidebar{padding-left:8px; min-height:200px}
    .header{margin-bottom: 20px;   background-color: #262626;
    <?php
    $bgimg = gila::option('theme.header-image');
    if($bgimg) echo "background: url($bgimg);"
    ?>
    background-size: cover;
    background-position-y: center;
    background-position-x: center;}
    .widget-social-icons {list-style: none;padding:0 }
    .widget-social-icons li{margin: 15px 10px 0 0; float: left; text-align: center; opacity: 0.8}
    .widget-social-icons li a i:before{
      width: 40px;
      margin: 0;
      color: #fff;
      font-size: 20px;
      line-height: 40px;
      display:inline-block;
      background: #060608;
    }
    .widget-social-icons li a i:hover:before{background: <?=$theme_color?>;}
    </style>
    <!--link href="lib/gila.min.css" rel="stylesheet"-->
</head>

<body>
    <div class="header" style="padding:0 10px">
    <div style="max-width:900px; margin:auto">
        <?php view::widget_area('body'); ?>
        <div class="gl-9">
          <h1><a href="<?=gila::config('base')?>" style="color:#f5f5f5;"><?=gila::config('title')?></a></h1>
          <div style="color:#ccc;margin-bottom:6px"><?=gila::config('slogan')?></div>
        </div>
        <!-- Navigation -->
        <div class="gl-9">
        <nav class="inline-flex g-navbar">
            <span style=""><?php view::widget('menu'); ?></span>
            <!--form method="get" class="inline-flex" action="<?=gila::make_url('blog')?>">
              <input name='search' class="g-input" value="<?=(isset($_GET['search'])?$_GET['search']:'')?>">
              <button class="g-btn" onclick='submit'>Search</button>
            </form-->
        </nav>
        </div>
    </div>
    </div>

    <div style="padding:0 10px">
    <div style="max-width:900px; margin:auto;">
