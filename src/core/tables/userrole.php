<?php

return [
  "name"=>"userrole",
  "title"=>"User Roles",
  "id"=>"id",
  'order-by'=>"updated DESC",
  'tools'=>["add","csv"],
  'csv'=>["id","userrole"],
  'lang'=>'core/lang/admin/',
  "commands"=> ["edit_popup"],
  'permissions'=>[
    'create'=>['admin','admin_userrole'],
    'read'=>['admin','admin_userrole'],
    'update'=>['admin','admin_userrole'],
    'delete'=>['admin','admin_userrole']
  ],
  "fields"=>[
    "id"=>[
      'title'=>"ID",
      'edit'=>false
    ],
    "userrole"=>[
      'title'=>"Role",
      'qtype'=>'VARCHAR(80)'
    ]
  ]
];
