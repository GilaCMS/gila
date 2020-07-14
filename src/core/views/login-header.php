<!DOCTYPE html>
<html lang="<?=Config::config('language')?>">

<head>
  <base href="<?=Config::base_url()?>">
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title><?=Config::config('title')?> - <?=($title??'')?></title>

  <?=View::css('core/gila.min.css')?>
  <?=View::css('lib/font-awesome/css/font-awesome.min.css')?>
</head>

<body style="background: var(--main-bg-color)">
