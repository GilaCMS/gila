<?php

$table = [
    'name'=> 'postcategory',
    'title'=> 'Categories',
    'tools'=>['add','csv'],
    'commands'=>['edit','clone'],
    'id'=>'id',
    //'list'=> ['id','title'],
    'csv'=> ['id','title'],
    'permissions'=>[
        'create'=>['admin'],
        'update'=>['admin']
    ],
    'fields'=> [
        'id'=> ['edit'=>false],
        'title'=> ['title'=>'Name']
    ]
];
