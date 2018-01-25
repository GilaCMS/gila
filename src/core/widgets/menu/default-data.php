<?php
global $db;
$widget_data=(object) array('menu' => []);

$widget_data->menu[] = ['url'=>'','title'=>'Home'];

$ql = "SELECT id,title FROM postcategory;";
$pages = $db->get($ql);
foreach ($pages as $p) {
    $widget_data->menu[] = ['url'=>"category/{$p[0]}/{$p[1]}",'title'=>$p[1]];
}

$ql = "SELECT id,title,slug FROM page WHERE publish=1;";
$pages = $db->get($ql);
foreach ($pages as $p) {
    $widget_data->menu[] = ['url'=>$p[2],'title'=>$p[1]];
}
