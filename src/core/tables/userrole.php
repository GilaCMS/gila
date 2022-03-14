<?php

return [
  "name"=>"userrole",
  "title"=>"Roles",
  "id"=>"id",
  'order-by'=>"updated DESC",
  'tools'=>["add_popup","csv"],
  'csv'=>["id","userrole"],
  'lang'=>'core/lang/admin/',
  "commands"=> ["edit_popup","delete"],
  'permissions'=>[
    'create'=>['admin','admin_userrole'],
    'read'=>['admin','admin_userrole'],
    'update'=>['admin','admin_userrole'],
    'delete'=>['admin','admin_userrole']
  ],
  'search_box'=>true,
  "fields"=>[
    "id"=>[
      'title'=>"ID",
      'edit'=>false,
      'create'=>false
    ],
    "userrole"=>[
      'title'=>"Role",
      'qtype'=>'VARCHAR(80)'
    ],
    "level"=>[
      'title'=>"Level",
      'qtype'=>'INT(1) DEFAULT 1',
      'type'=>'select',
      'options'=>[0=>'0',1=>'1',2=>'2',3=>'3',4=>'4',5=>'5',6=>'6',7=>'7',8=>'8',9=>'9',10=>'10']
    ],
    "description"=>[
      'title'=>"Description",
      'qtype'=>'VARCHAR(200)'
    ]
  ]
];
