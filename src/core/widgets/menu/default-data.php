<?php
use core\models\page as page;

global $db;
$widget_data=(object) array('menu' => []);

$widget_data->menu[] = ['url'=>'','title'=>__('Home')];

$ql = "SELECT id,title FROM postcategory;";
$pages = $db->get($ql);
foreach ($pages as $p) {
    $widget_data->menu[] = ['url'=>"category/{$p[0]}/{$p[1]}",'title'=>$p[1]];
}

foreach (page::genPublished() as $p) {
    $widget_data->menu[] = ['url'=>$p['slug'],'title'=>$p['title']];
}
