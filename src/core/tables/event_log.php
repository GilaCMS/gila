<?php

return [
    'name'=> 'event_log',
    'title'=> 'Event Log',
    'pagination'=> 25,
    'id'=>'id',
    'tools'=>['csv'],
    'commands'=> ['edit_popup','delete'],
    'qkeys'=>['user_id'],
    'permissions'=>[
      'read'=>['admin'],
      'create'=>['admin'],
      'update'=>['admin'],
      'delete'=>['admin']
    ],
    'fields'=> [
      'id'=> [
        'title'=>'ID',
        'style'=>'width:5%',
        'edit'=>false,
        'create'=>false
      ],
      'created'=> [
        'qtype'=>'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP'
      ],
      'type'=> [
        'qtype'=> 'VARCHAR(30)'
      ],
      'user_id'=> [
        'qtype'=> 'INT DEFAULT 0'
      ],
      'data'=> [
        'qtype'=>'TEXT'
      ]
    ]
];
