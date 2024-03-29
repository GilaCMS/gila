<?php

return [
  'name'=> 'postcategory',
  'title'=> 'Categories',
  'tools'=>['add_popup','csv'],
  'commands'=>['edit_popup','delete'],
  'id'=>'id',
  'csv'=> ['id','title'],
  'permissions'=>[
    'read'=>['admin','editor'],
    'update'=>['admin','editor'],
    'create'=>['admin','editor'],
  ],
  'fields'=> [
      'id'=> [
        'edit'=>false,
        'create'=>false
      ],
      'title'=> [
        'title'=>'Name',
        'qtype'=>'varchar(80) DEFAULT NULL'
      ],
      'slug'=> [
        'title'=>'Slug',
        'qtype'=>'varchar(120) DEFAULT NULL'
      ],
      'description'=> [
        'title'=>'Description',
        'list'=>false,
        'input_type'=>'textarea',
        'qtype'=>'varchar(200) DEFAULT NULL'
        ]
  ],
  'events'=> [
    ['change', function (&$row) {
      if ($row['slug']=='') {
        $row['slug'] = Slugify::text($row['title']);
      }
    }]
  ]
];
