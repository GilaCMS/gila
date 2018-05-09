<?php

$table = [
    'name'=> 'post',
    'title'=> 'Posts',
    'pagination'=> 15,
    'id'=>'id',
    'tools'=>['new_post','csv'],
    'list'=>['id','title','user_id','updated','categories','publish','commands'],
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
        "categories"=>[
            'edit'=>true,
            'type'=>'meta',
            "mt"=>['postmeta', 'post_id', 'value'],
            'metatype'=>['vartype', 'category'],
            "title"=>"Categories",
            "qoptions"=>"id,title FROM postcategory"
        ],
        "tags"=>[
            'list'=>false,
            'edit'=>true,
            'type'=>'meta',
            "mt"=>['postmeta', 'post_id', 'value'],
            'metatype'=>['vartype', 'tag'],
            "title"=>"Tags",
            //"qoptions"=>"id,title FROM postcategory"
        ],
        'publish'=> [
            'title'=>'Public',
            'style'=>'width:8%',
            'type'=>'checkbox','edit'=>true
        ],
        'commands'=>[
            'title'=>'','qcolumn'=>"''",'eval'=>"dv='<a href=\"admin/posts/'+rv.id+'\">Edit</a>';"
        ],
        'post'=>[
            'list'=>false, 'type'=>'text'
        ]
    ]
];
