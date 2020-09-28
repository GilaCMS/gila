<?php

return [
  'name'=> 'page',
  'title'=> 'Pages',
  'pagination'=> 15,
  'id'=>'id',
  'tools'=>['add_popup','csv'],
  'csv'=> ['id','title','slug','updated','publish','page'],
  'commands'=> ['edit_popup','blocks_popup','delete'],
  'lang'=>'core/lang/admin/',
  'qkeys'=>['slug','publish'],
  'js'=>['src/core/tables/page.js','src/core/assets/admin/blocks_btn.js'],
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
      'qtype'=>'varchar(80) DEFAULT NULL',
      'alt'=>'('.Gila\Config::tr('Home').')'
    ],
    'template'=> [
      'title'=>'Template',
      'template'=>'page',
      'type'=>'template',
      'edit'=>true,
      'qtype'=>'varchar(30) DEFAULT NULL'
    ],
    'description'=> [
      'title'=>'Description',
      'input-type'=>'textarea',
      'qtype'=>'varchar(200) DEFAULT NULL'
    ],
    'publish'=> [
      'title'=>'Public',
      'style'=>'width:8%',
      'type'=>'checkbox',
      'edit'=>true,
      'qtype'=>'INT(1) DEFAULT NULL'
    ],
    'content'=> [
      'title'=>'Content',
      'helptext'=>'This field is deprecated, move the content in text blocks with page builder',
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
    ],
    'blocks'=> [
      'list'=> false,
      'edit'=> false,
      'qtype'=> 'TEXT'
    ]
  ]
];
