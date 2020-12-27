<?php

return [
  'name'=> 'page',
  'title'=> 'Pages',
  'pagination'=> 5,
  'id'=>'id',
  'tools'=>['add_popup','csv'],
  'csv'=> ['id','title','slug','updated','publish','page'],
  'commands'=> ['blocks','delete'],
  'qactions'=> ['title'=>['edit_popup','blocks','delete']],
  'lang'=>'core/lang/admin/',
  'qkeys'=>['slug','publish'],
  'js'=>['src/core/tables/page.js','src/core/assets/admin/blocks_btn.js'],
  'permissions'=>[
    'read'=>['admin', 'editor'],
    'create'=>['admin', 'editor'],
    'update'=>['admin', 'editor'],
    'delete'=>['admin', 'editor']
  ],
  'fields'=> [
    'id'=> [
      'title'=>'ID',
      'style'=>'width:5%',
      'edit'=>false,
      'create'=>false
    ],
    'title'=> [
      'title'=>'Title',
      'qtype'=>'varchar(80) DEFAULT NULL',
      'group'=>'title'
    ],
    'slug'=> [
      'title'=>'Route',
      'qtype'=>'varchar(80) DEFAULT NULL',
      'alt'=>'('.Gila\Config::tr('Home').')',
      'group'=>'title'
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
      'qtype'=>'varchar(200) DEFAULT NULL',
      'list'=>false,
      'group'=>'title'
    ],
    'publish'=> [
      'title'=>'Public',
      'style'=>'width:8%',
      'type'=>'checkbox',
      'edit'=>true,
      'create'=>false,
      'qtype'=>'INT(1) DEFAULT NULL'
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
      'create'=> false,
      'qtype'=> 'TEXT'
    ]
  ],
  'events'=>[
    ['create',function (&$row) {
      if ($row['slug']=='') {
        $row['slug'] = Slugify::text($row['title']);
      }
    }]
  ]
];
