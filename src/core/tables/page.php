<?php

return [
  'name'=> 'page',
  'title'=> 'Pages',
  'pagination'=> 15,
  'id'=>'id',
  'tools'=>['add_popup'],
  'csv'=> ['id','title','slug','updated','publish'],
  'commands'=> ['blocks','page_seo','delete','clone'],
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
      'qtype'=>'VARCHAR(80) DEFAULT NULL',
      'group'=>'title',
      'required'=>true
    ],
    'slug'=> [
      'title'=>'Route',
      'qtype'=>'VARCHAR(80) DEFAULT NULL',
      'alt'=>'('.Gila\Config::tr('Home').')',
      'group'=>'title',
      'create'=>false,
      'helptext'=>'Leave empty if this is the homepage',
      'helptext_es'=>'Dejar vacía si esta es la página de inicio'
    ],
    'description'=> [
      'title'=>'Description',
      'input_type'=>'textarea',
      'qtype'=>'VARCHAR(200) DEFAULT NULL',
      'list'=>false,
      'create'=>false,
      'group'=>'title',
      'helptext'=>'This is the text that will be displayed in the listing of the page. For better SEO, use 120-160 letters.',
      'helptext_es'=>'Este es el texto que se mostrará en el listado de la página. Para un mejor SEO, use 120-160 letras..'
    ],
    'template'=> [
      'title'=>'Template',
      'template'=>'page',
      'type'=>'template',
      'edit'=>true,
      'create'=>false,
      'list'=>false,
      'qtype'=>'VARCHAR(30) DEFAULT NULL'
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
      'default'=>1,
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
    ],
    'image'=> [
      'list'=> false,
      'create'=> false,
      'type'=>'media2',
      'qtype'=> 'VARCHAR(120)'
    ],
    'meta'=> [
      'list'=> false,
      'type'=>'list',
      'fields'=>[
        'content'=>[],
        'value'=>[]
      ]
    ]
  ],
  'events'=>[
    ['create', function (&$row) {
      if ($row['slug']=='') {
        $row['slug'] = Slugify::text($row['title']);
      }
    }],
    ['change', function (&$row) {
      global $db;
      $query = "SELECT id FROM `page` WHERE publish=1 AND slug=? AND title!=? AND `language`=?";
      if ($row['publish']==1 && $other=$db->getOne($query, [$row['slug'], $row['title'], $row['language']])) {
        Table::$error = __('Another page has the same path')." (ID:{$other['id']})";
      }
    }]
  ]
];
