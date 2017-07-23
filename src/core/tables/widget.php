<?php

$widget_areas = ['x'=>'(None)'];
foreach (gila::$widget_area as $value) {
    $widget_areas[$value] = $value;
}
$widgets = [];
foreach (gila::$widget as $k=>$value) {
    $widgets[$k] = $k;
}

$table = [
    'name'=> 'widget',
    'title'=> 'Widgets',
    'id'=>'id',
    'tools'=>['add'],
    'commands'=>['edit_widget','delete'],
    'csv'=> ['id','title','slug','updated','publish','page'],
    'fields'=> [
        'id'=> ['title'=>'ID', 'edit'=>false],
        'widget'=> ['title'=>'Widget', 'options'=>$widgets,  'create'=>true], //'edit'=>false,
        //'title'=> [],
        'area'=> ['title'=>'Widget Area', 'options'=>$widget_areas],
        'pos'=> ['title'=>'Position'],
        'data'=> ['title'=>'Data', 'show'=>false, 'edit'=>true, 'qcolumn'=>"REPLACE(data,'\"','\\\"')", 'type'=>'text'],
    ]
];
