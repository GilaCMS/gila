<?php

$table = [
    'name'=> 'post',
    'title'=> 'Posts',
    'pagination'=> 15,
    'id'=>'id',
    'tools'=>['new_post','csv'],
    'csv'=> ['id','title','slug','user_id','updated','publish','post'],
    'commands'=> ['delete'],
    'search-boxes'=> ['title','user_id','updated'],
    'fields'=> [
        'id'=> ['style'=>'width:5%'],
        'title'=> [],
        /*'slug'=> [],*/
        'user_id'=> [
            'search-options'=>[0=>'Zero',1=>'One']
        ],
        'updated'=> [
            'type'=>'date',
            'searchbox'=>'period'
        ],
        'publish'=> [
            'style'=>'width:5%',
            'type'=>'checkbox','edit'=>false
        ],
        'commands'=>[
            'title'=>'','qcolumn'=>"''",'eval'=>"dv='<a href=\"admin/posts/'+rv.id+'\">Edit</a>';"
        ]
    ]
];
