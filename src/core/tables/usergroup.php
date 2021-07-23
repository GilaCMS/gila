<?php

return [
  "name"=>"usergroup",
  "title"=>"Groups",
  "id"=>"id",
  'order-by'=>"updated DESC",
  'tools'=>["add_popup","csv"],
  'csv'=>["id","usergroup"],
  'lang'=>'core/lang/admin/',
  "commands"=> ["edit_popup","delete"],
  'permissions'=>[
    'create'=>['admin','admin_user'],
    'read'=>['admin','admin_user'],
    'update'=>['admin','admin_user'],
    'delete'=>['admin','admin_user']
  ],
  'search_box'=>true,
  "fields"=>[
    "id"=>[
      'title'=>"ID",
      'edit'=>false,
      'create'=>false
    ],
    "usergroup"=>[
      'title'=>"Group",
      'qtype'=>'VARCHAR(80)'
    ],
    "description"=>[
      'title'=>"Description",
      'qtype'=>'VARCHAR(200)'
    ]
  ]
];
