<?php

return [
  'name'=> 'page',
  'title'=> 'Pages',
  'pagination'=> 15,
  'id'=>'id',
  'tools'=>['add','csv'],
  'csv'=> ['id','title','slug','updated','publish','page'],
  'commands'=> ['edit','delete'],
  'lang'=>'core/lang/admin/',
  'qkeys'=>['slug','publish'],
  'permissions'=>[
    'create'=>['admin', 'editor'],
    'update'=>['admin', 'editor'],
    'delete'=>['admin', 'editor']
  ],
  'fields'=> [
    'id'=> [
      'title'=>'ID',
      'style'=>'width:5%',
      'edit'=>false
    ],
    'title'=> [
      'title'=>'Title',
      'qtype'=>'varchar(80) DEFAULT NULL'
    ],
    'slug'=> [
      'title'=>'Path',
      'qtype'=>'varchar(80) DEFAULT NULL'
    ],
    'publish'=> [
      'title'=>'Public',
      'style'=>'width:8%',
      'type'=>'checkbox',
      'edit'=>true,
      'qtype'=>'INT(1) DEFAULT NULL'
    ],
    'template'=> [
      'title'=>'Template',
      'template'=>'page',
      'type'=>'template',
      'edit'=>true,
      'qtype'=>'varchar(30) DEFAULT NULL'
    ],
    'content'=> [
      'title'=>'Content',
      'list'=>false,
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
      'list'=>false,
      'create'=>false,
      'qtype'=>'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
    ]
  ]
];
