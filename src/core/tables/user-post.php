<?php

return [
  'name'=> 'post',
  'title'=> 'Posts',
  'pagination'=> 15,
  'id'=>'id',
  'tools'=>['add','csv'],
  'csv'=> ['id','title','slug','updated','publish','post'],
  'commands'=> ['edit','delete'],
  'lang'=>'core/lang/admin/',
  'permissions'=>[
    'read'=>['writer'],
    'create'=>['writer'],
    'update'=>['writer'],
    'delete'=>['writer']
  ],
  'filters'=> ["user_id"=>session::user_id()],
  'search-box'=>true,
  'fields'=> [
    'id'=> [
      'title'=>'ID',
      'style'=>'width:5%',
      'create'=>false,'edit'=>false
    ],
    'title'=> [
      'title'=>'Title',
    ],
    'description'=> [
      'title'=>'Description', 'list'=>false
    ],
    'thumbnail'=> [
      'type'=>'media',
      'list'=>false,
      'type'=>'meta',
      'input-type'=>'media',
      'meta-csv'=>true,
      "mt"=>['postmeta', 'post_id', 'value'],
      'metatype'=>['vartype', 'thumbnail']
    ],
    'slug'=> ['list'=>false],
    'user_id'=> [
      'title'=>'User',
      'type'=>'select', 'default'=>session::user_id(),
      'qoptions'=>"SELECT id, username FROM user","edit"=>false
    ],
    'updated'=> [
      'title'=>'Last updated',
      'type'=>'date',
      'searchbox'=>'period',
      'edit'=>false,'create'=>false
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
      "title"=>"Tags",
    ],
    'publish'=> [
      'title'=>'Public',
      'style'=>'width:8%',
      'type'=>'checkbox','edit'=>false
    ],
    'post'=>[
      'list'=>false, 'title'=>'Post', 'edit'=>true, 'type'=>'textarea', 'input-type'=>'tinymce', 'allow-tags'=>true
    ]
  ],
  'events'=>[
    ['change',function(&$row) {
      if($row['slug']=='') {
        $slugify = new Cocur\Slugify\Slugify();
        $row['slug'] = $slugify->slugify($row['title']);
      }
    }]
  ]
];
