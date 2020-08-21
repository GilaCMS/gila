<?php

return [
  'name'=>'user_notification',
  'id'=>'id',
  'fields'=>[
    'id'=>[],
    'user_id'=>[
      'qtype'=>'INT'
    ],
    'type'=>[
      'qtype'=>'VARCHAR(30)'
    ],
    'details'=>[
      'qtype'=>'VARCHAR(200)'
    ],
    'url'=>[
      'qtype'=>'VARCHAR(200)'
    ],
    'unread'=>[
      'qtype'=>'INT(1) DEFAULT 1'
    ],
    'created'=>[
      'qtype'=>'TIMESTAMP'
    ]
  ]
];
