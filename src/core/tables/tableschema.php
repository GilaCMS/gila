<?php

return [
  'name'=> 'tableschema',
  'title'=> 'Table Schemas',
  'pagination'=> 15,
  'tools'=>['add_popup','csv'],
  'commands'=>['edit_popup','delete'],
  'id'=>'id',
  'lang'=>'core/lang/admin/',
  'permissions'=>[
    'read'=>['admin'],
    'create'=>['admin'],
    'update'=>['admin'],
    'delete'=>['admin']
  ],
  'fields'=> [
    'id'=> [
      'title'=>'ID',
      'edit'=>false,
      'create'=>false
    ],
    'name'=> [
      'title'=>'Name',
      'qtype'=>'VARCHAR(120)'
    ],
    'data'=> [
      'title'=>'Schema',
      'qtype'=>'TEXT',
      'input_type'=>'codemirror',
      'list'=>false
    ]
  ],
  'events'=>[
    ['change', function (&$row) {
      TableSchema::update(json_decode($row['data'], true));
    }]
  ]
];
