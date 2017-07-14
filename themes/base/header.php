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

    <link href="lib/gila.min.css" rel="stylesheet">
</head>

<body>

    <!-- Navigation -->
    <nav>
        <?php view::widget('menu'); ?>
    </nav>
