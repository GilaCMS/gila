<?php

return [
  'name'=> 'post',
  'title'=> 'Posts',
  'pagination'=> 15,
  'id'=>'id',
  'tools'=>['add','csv'],
  'csv'=> ['id','title','slug','user_id','updated','publish','post'],
  'commands'=> ['edit','delete'],
  'lang'=>'core/lang/admin/',
  'qkeys'=>['slug','publish','user_id'],
  'meta-table'=>['postmeta', 'post_id', 'vartype', 'value'],
  'permissions'=>[
    'read'=>['admin','editor'],
    'create'=>['admin','editor'],
    'update'=>['admin','editor'],
    'delete'=>['admin','editor']
  ],
  'search-box'=>true,
  'search-boxes'=> ['user_id'],
  'fields'=> [
    'id'=> [
      'title'=>'ID',
      'style'=>'width:5%',
      'create'=>false,'edit'=>false
    ],
    'thumbnail'=> [
      'title'=>'Thumbnail',
      'type'=>'meta',
      'input-type'=>'media',
      'meta-csv'=>true,
      "mt"=>['postmeta', 'post_id', 'value'],
      'metatype'=>['vartype', 'thumbnail']
    ],
    'title'=> [
      'title'=>'Title',
      'qtype'=>'varchar(80) DEFAULT NULL'
    ],
    'slug'=> [
      'list'=>false,
      'qtype'=>'varchar(80) CHARACTER SET latin1 DEFAULT NULL'
    ],
    'description'=> [
      'title'=>'Description',
      'list'=>false,
      'qtype'=>'varchar(200)'
    ],
    'user_id'=> [
      'title'=>'User',
      'type'=>'select',
      'qoptions'=>"SELECT id, username FROM user",
      'qtype'=>'varchar(80) CHARACTER SET latin1 DEFAULT NULL'
    ],
    "categories"=>[
      'edit'=>true,
      'type'=>'meta',
      "mt"=>['postmeta', 'post_id', 'value'],
      'metatype'=>['vartype', 'category'],
      "title"=>"Categories",
      "qoptions"=>"SELECT id,title FROM postcategory;"
    ],
    "tags"=>[
      'list'=>false,
      'edit'=>true,
      'type'=>'meta',
      'meta-csv'=>true,
      "mt"=>['postmeta', 'post_id', 'value'],
      'metatype'=>['vartype', 'tag'],
      "title"=>"Tags"
    ],
    'post'=>[
      'list'=>false,
      'title'=>'Post',
      'edit'=>true,
      'type'=>'textarea',
      'input-type'=>'tinymce',
      'allow-tags'=>true,
      'qtype'=>'TEXT'
    ],
    'publish'=> [
      'title'=>'Public',
      'style'=>'width:8%',
      'type'=>'checkbox',
      'edit'=>true,
      'qtype'=>'INT(1) DEFAULT NULL'
    ],
    'updated'=> [
      'title'=>'Updated',
      'type'=>'date',
      'searchbox'=>'period',
      'edit'=>false,
      'create'=>false,
      'qtype'=>'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
    ],
    'created'=> [
      'title'=>'Created',
      'type'=>'date',
      'list'=>false,
      'edit'=>false,
      'create'=>false,
      'qtype'=>'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP'
    ]
  ],
  'events'=>[
    ['change',function(&$row) {
      if($row['slug']=='') {
        $row['slug'] = Slugify::text($row['title']);
      }
    }]
  ]
];
