<?php

return [
  'name'=> 'redirect',
  'title'=> '301 Redirects',
  'pagination'=> 15,
  'id'=>'id',
  'tools'=>['add_popup'],
  'commands'=> ['edit_popup','delete'],
  'lang'=>'core/lang/admin/',
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
    'from_slug'=> [
      'title'=>'From',
      'qtype'=>'varchar(120) DEFAULT NULL'
    ],
    'to_slug'=> [
      'title'=>'To',
      'qtype'=>'varchar(120) DEFAULT NULL'
    ],
    'active'=> [
      'style'=>'width:8%',
      'type'=>'checkbox',
      'default'=>1,
      'qtype'=>'INT(1) DEFAULT 1'
    ]
  ]
];
