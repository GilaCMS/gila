<?php

return [
  'name'=>'sessions',
  'id'=>'id',
  'fields'=>[
    'id'=>[],
    'user_id'=>[
      'qtype'=>'INT'
    ],
    'gsessionid'=>[
      'qtype'=>'VARCHAR(200)'
    ],
    'user_agent'=>[
      'qtype'=>'VARCHAR(200)'
    ],
    'updated'=>[
      'qtype'=>'TIMESTAMP DEFAULT CURRENT_TIMESTAMP()'
    ]
  ]
];
