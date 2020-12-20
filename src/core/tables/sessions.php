<?php

return [
  'name'=>'sessions',
  'id'=>'id',
  'qkeys'=>['gsessionid'],
  'fields'=>[
    'id'=>[],
    'user_id'=>[
      'qtype'=>'INT'
    ],
    'gsessionid'=>[
      'qtype'=>'VARCHAR(120)'
    ],
    'ip_address'=>[
      'qtype'=>'VARCHAR(45)'
    ],
    'user_agent'=>[
      'qtype'=>'VARCHAR(200)'
    ],
    'updated'=>[
      'qtype'=>'TIMESTAMP DEFAULT CURRENT_TIMESTAMP()'
    ],
    'data'=>[
      'qtype'=>'TEXT'
    ]
  ]
];
