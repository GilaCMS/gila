<head>
<base href="<?=gila::config('base')?>">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<?php
    foreach(view::$meta as $key=>$value) echo '<meta name="'.$key.'" content="'.$value.'">';
    echo '<title>'.gila::config('base').'</title>';
    event::fire('head.meta');
    foreach(view::$stylesheet as $link) echo '<link href="'.$link.'" rel="stylesheet">';
    foreach(view::$scriptAsync as $src) echo '<script href="'.$link.'" rel="stylesheet">';
    event::fire('head');
?>
<link rel="icon" type="image/png" href="assets/favicon.ico">
</head>
