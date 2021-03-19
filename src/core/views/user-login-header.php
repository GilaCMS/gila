<!DOCTYPE html>
<html lang="<?=Gila\Config::lang()?>">

<head>
  <base href="<?=Gila\Config::base()?>">
  <?php Gila\View::$stylesheet=[]?>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title><?=Gila\Config::get('title')?> - <?=($title??'')?></title>

  <?=Gila\View::css('core/gila.min.css')?>
  <?=Gila\View::css('lib/font-awesome/css/font-awesome.min.css')?>
</head>

<body style="background: var(--main-bg-color)">
