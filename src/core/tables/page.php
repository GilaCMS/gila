<?php

$table = [
    'name'=> 'page',
    'title'=> 'Pages',
    'pagination'=> 15,
    'id'=>'id',
    'tools'=>['new_page','csv'],
    'csv'=> ['id','title','slug','updated','publish','page'],
    'commands'=> ['delete'],
    'lang'=>'core/lang/admin/',
    'permissions'=>[
        'create'=>['admin'],
        'update'=>['admin'],
        'delete'=>['admin']
    ],
    'fields'=> [
        'id'=> [
            'title'=>'ID',
            'style'=>'width:5%'
        ],
        'title'=> [
            'title'=>'Title',
        ],
        'slug'=> [
            'title'=>'Slug',
        ],
        //'updated'=> [],
        'publish'=> [
            'title'=>'Public',
            'style'=>'width:8%',
            'type'=>'checkbox','edit'=>true
        ],
        'commands'=>[
            'title'=>'','qcolumn'=>"''",'eval'=>"dv='<a href=\"admin/pages/'+rv.id+'\">Edit</a>';"
        ]
        //'page'=> ['list'=>false]
    ]
];
