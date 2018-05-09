<?php

$table = [
    'name'=> 'postcategory',
    'title'=> 'Categories',
    'tools'=>['add','csv'],
    'commands'=>['edit'],
    'id'=>'id',
    'list'=> ['id','title'],
    'csv'=> ['id','title'],
    'fields'=> [
        'id'=> ['edit'=>false],
        'title'=> ['title'=>'Name']
    ]
];

//,'type'=>'text'
