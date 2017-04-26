<?php

$table = [
    'name'=> 'postcategory',
    'title'=> 'Categories',
    'pagination'=> 15,
    'tools'=>['add','csv'],
    'commands'=>['edit'],
    'id'=>'id',
    'csv'=> ['id','name'],
    'fields'=> [
        'id'=> ['edit'=>false],
        'title'=> ['title'=>'Name','type'=>'text']
    ]
];
