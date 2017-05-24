<?php

$table = [
    'name'=> 'post',
    'title'=> 'Posts',
    'pagination'=> 15,
    'id'=>'id',
    'tools'=>['new_post','csv'],
    'csv'=> ['id','title','slug','user_id','updated','publish','post'],
    'commands'=> ['delete'],
    'fields'=> [
        'id'=> ['style'=>'width:5%'],
        'title'=> [],
        /*'slug'=> [],*/
        'user_id'=> [],
        'updated'=> [],
        'publish'=> ['style'=>'width:5%'],
        //'post'=> ['list'=>false],
        'commands'=>[
            'title'=>'','qcolumn'=>"''",'eval'=>"dv='<a href=\"admin/posts/'+rv.id+'\">Edit</a>';"
        ]
    ]
];
