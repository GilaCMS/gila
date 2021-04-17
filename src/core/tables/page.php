<?php

return [
  'name'=> 'page',
  'title'=> 'Pages',
  'pagination'=> 15,
  'id'=>'id',
  'tools'=>['add_popup'],
  'csv'=> ['id','title','slug','updated','publish'],
  'commands'=> ['blocks','delete','clone'],
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
  'search_box'=>true,
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
      'group'=>'title',
      'create'=>false
    ],
    'description'=> [
      'title'=>'Description',
      'input_type'=>'textarea',
      'qtype'=>'varchar(200) DEFAULT NULL',
      'list'=>false,
      'create'=>false,
      'group'=>'title',
      'helptext'=>'This is the text that will be displayed in the listing of the page. For better SEO, use 120-160 letters.'
    ],
    'template'=> [
      'title'=>'Template',
      'template'=>'page',
      'type'=>'template',
      'edit'=>true,
      'create'=>false,
      'list'=>false,
      'qtype'=>'varchar(30) DEFAULT NULL'
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
