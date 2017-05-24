<?php

$widget_areas = [];
foreach (gila::$widget_area as $value) {
    $widget_areas[$value] = $value;
}

$table = [
    'name'=> 'widget',
    'title'=> 'Widgets',
    'id'=>'id',
    'tools'=>['add'],
    'commands'=>['edit','delete'],
    'csv'=> ['id','title','slug','updated','publish','page'],
    'fields'=> [
        'id'=> ['title'=>'ID', 'edit'=>false],
        'widget'=> ['title'=>'Widget'], //'edit'=>false, 'create'=>true
        //'title'=> [],
        'area'=> ['title'=>'Widget Area', 'options'=>$widget_areas],
        'pos'=> ['title'=>'Position'],
        'data'=> ['title'=>'Data', 'show'=>false, 'edit'=>true, 'qcolumn'=>"REPLACE(data,'\"','\\\"')", 'type'=>'text'],
    ]
];
