<?php

$table = [
    'name'=> 'post',
    'title'=> 'Posts',
    'pagination'=> 15,
    'id'=>'id',
    'tools'=>['new_post','csv'],
    'csv'=> ['id','title','slug','user_id','updated','publish','post'],
    'commands'=> ['delete'],
    'search-boxes'=> ['title','user_id'],
    'fields'=> [
        'id'=> [
            'title'=>'ID',
            'style'=>'width:5%'
        ],
        'title'=> [
            'title'=>'Title',
        ],
        /*'slug'=> [],*/
        'user_id'=> [
            'title'=>'User',
            'qoptions'=>"id AS `Index`, username AS `Text` FROM user"
        ],
        'updated'=> [
            'title'=>'Last updated',
            'type'=>'date',
            'searchbox'=>'period'
        ],
        'publish'=> [
            'title'=>'Public',
            'style'=>'width:8%',
            'type'=>'checkbox','edit'=>true
        ],
        'commands'=>[
            'title'=>'','qcolumn'=>"''",'eval'=>"dv='<a href=\"admin/posts/'+rv.id+'\">Edit</a>';"
        ]
    ]
];
