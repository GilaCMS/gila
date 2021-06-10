<?php

return [
  'name'=> 'post',
  'title'=> 'Posts',
  'pagination'=> 15,
  'cache'=>true,
  'id'=>'id',
  'tools'=>['add_popup','csv'],
  'bulk_actions'=>['approve'],
  'approve'=>['publish','1'],
  'csv'=> ['id','title','slug','user_id','updated','publish','post'],
  'commands'=> ['edit_popup','delete','clone'],
  'qactions'=> ['title'=>['edit_popup','delete']],
  'lang'=>'core/lang/admin/',
  'qkeys'=>['slug','publish','user_id'],
  'meta_table'=>['postmeta', 'post_id', 'vartype', 'value'],
  'js'=>['src/core/tables/post.js'],
  'permissions'=>[
    'read'=>['admin','editor'],
    'create'=>['admin','editor'],
    'update'=>['admin','editor'],
    'delete'=>['admin','editor']
  ],
  'search_box'=>true,
  'search_boxes'=> ['categories','user_id'],
  'fields'=> [
    'id'=> [
      'title'=>'ID',
      'style'=>'width:5%',
      'create'=>false,'edit'=>false
    ],
    'thumbnail'=> [
      'title'=>'Thumbnail',
      'type'=>'meta',
      'display_type'=>'media',
      'input_type'=>'media2',
      'meta_csv'=>true,
      'meta_key'=>['vartype', 'thumbnail']
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
      'input_type'=>'textarea',
      'qtype'=>'varchar(200)'
    ],
    'user_id'=> [
      'title'=>'User',
      'type'=>'select',
      'qoptions'=>"SELECT id, username FROM user",
      'qtype'=>'int(11) unsigned DEFAULT NULL',
      'list'=>false
    ],
    "categories"=>[
      'edit'=>true,
      'type'=>'meta',
      'meta_key'=>'category',
      "title"=>"Categories",
      "qoptions"=>"SELECT id,title FROM postcategory;"
    ],
    "tags"=>[
      'list'=>false,
      'edit'=>true,
      'type'=>'meta',
      'meta_csv'=>true,
      'meta_key'=>'tag',
      "title"=>"Tags"
    ],
    'language'=> [
      'type'=>'language',
      'qtype'=>'VARCHAR(2) DEFAULT NULL'
    ],
    'publish'=> [
      'title'=>'Public',
      'style'=>'width:8%',
      'type'=>'checkbox',
      'edit'=>true,
      'qtype'=>'INT(1) DEFAULT NULL'
    ],
    'post'=>[
      'list'=>false,
      'title'=>'Post',
      'edit'=>true,
      'type'=>'textarea',
      'input_type'=>'tinymce',
      'allow_tags'=>true,
      'qtype'=>'TEXT'
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
  ]/*,
  'events'=>[
    ['change',function (&$row) {
      if ($row['slug']=='') {
        $row['slug'] = Slugify::text($row['title']);
      }
    }]
  ]*/
];
