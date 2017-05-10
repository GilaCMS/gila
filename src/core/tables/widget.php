<?php

$table = [
    'name'=> 'page',
    'title'=> 'Pages',
    'pagination'=> 15,
    'id'=>'id',
    'tools'=>['new_page','csv'],
    'csv'=> ['id','title','slug','updated','publish','page'],
    'fields'=> [
        'id'=> [],
        'title'=> [],
        'slug'=> [],
        'updated'=> [],
        'publish'=> [],
        'publish'=> [],
        'commands'=>[
            'title'=>'','qcolumn'=>"''",'eval'=>"dv='<a href=\"admin/pages/'+rv.id+'\">Edit</a>';"
        ]
        //'page'=> ['list'=>false]
    ]
];
