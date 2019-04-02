<?php

return [
    'name'=> 'postcategory',
    'title'=> 'Categories',
    'tools'=>['add','csv'],
    'commands'=>['edit','clone'],
    'id'=>'id',
    'csv'=> ['id','title'],
    'permissions'=>[
      'read'=>['admin','editor'],
      'update'=>['admin','editor'],
      'create'=>['admin','editor'],
    ],
    'fields'=> [
        'id'=> ['edit'=>false,'create'=>false],
        'title'=> ['title'=>'Name']
    ]
];
