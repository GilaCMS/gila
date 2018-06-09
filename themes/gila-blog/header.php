<!DOCTYPE html>
<html lang="<?=gila::config('language')?>">
<?php view::head()?>
<style>
<?php $theme_color=gila::option('theme.color','orangered'); ?>
body{font-family:'Arial', sans-serif;}
h1,h2,h3,.widget-title,.header{font-family:Arial,sans-serif;}
.widget-title,.header{font-size:1.1em}
.widget{margin-top: 20px; padding: 0 8px}
.widget-title{border-bottom: 4px solid <?=$theme_color?>; display:inline-block;}
.widget-body{border-top: 1px solid #ccc}
.widget:before{content: ""; width:100%; margin-top: 12px; border-bottom: 1px solid #ccc;}
.widget .g-nav.vertical li{border-bottom: 1px solid #ddd}
.widget .g-nav.vertical li a{color: #181818;padding: 4px 12px}
.widget .g-nav.vertical li a:hover{color: <?=$theme_color?>;}
.post-review a{color: #181818;}
.post-review a:hover{color: <?=$theme_color?>;}
.sidebar{padding-left:8px; min-height:200px}
.header{margin-bottom: 20px;   background-color: #262626;
<?php
$bgimg = gila::option('theme.header-image');
if($bgimg) {
    $srcset = view::thumb_srcset($bgimg);
    echo "background: url({$srcset[0]});";
    echo "background-image: -webkit-image-set(url({$srcset[0]}) 1x, url({$srcset[1]}) 2x);";
}
?>
background-size: cover;
background-position-y: <?=gila::option('theme.header-position','center')?>;
background-position-x: center;}
footer{background:#464a49;margin-top:10px;color:white}
.footer-text,footer a,footer a:hover{color:#ccc; }
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
.g-navbar li ul li a{color:inherit}
.g-nav li ul{border-width:0; background: #181818; margin-top:-2px}
</style>

<body>
    <div class="header" style="padding:0 10px;">
    <div style="max-width:1000px; margin:auto;">
        <?php view::widget_area('body'); ?>
        <div class="gl-9" style="height:200px;text-shadow:0 0 6px black">
          <h1><a href="<?=gila::config('base')?>" style="color:#f5f5f5;"><?=gila::config('title')?></a></h1>
          <div style="color:#f5f5f5;margin-bottom:6px"><?=gila::config('slogan')?></div>
        </div>
        <!-- Navigation -->
        <div class="gl-9">
        <nav class="inline-flex g-navbar">
            <span style=""><?php view::menu('mainmenu'); ?></span>
        </nav>
        </div>
    </div>
    </div>

    <div style="padding:0 10px">
    <div style="max-width:1000px; margin:auto;">
