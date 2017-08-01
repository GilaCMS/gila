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
    <!--link href="lib/gila.min.css" rel="stylesheet"-->
</head>

<body>
  <div  style="max-width:1000px; margin:auto">
    <div class="header">
        <?php view::widget_area('body'); ?>
        <div>
          <h1><a href="<?=gila::config('base')?>"><?=gila::config('title')?></a></h1>
        </div>
        <!-- Navigation -->
        <nav class="inline-flex fullwidth">
            <span class="fullwidth"><?php view::widget('menu'); ?></span>
            <!--form method="get" class="inline-flex" action="<?=gila::make_url('blog')?>">
              <input name='search' class="g-input" value="<?=(isset($_GET['search'])?$_GET['search']:'')?>">
              <button class="g-btn" onclick='submit'>Search</button>
            </form-->
      </nav>
    </div>
