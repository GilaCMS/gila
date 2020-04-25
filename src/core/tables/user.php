<?php

return [
  'name'=> 'user',
  'title'=> 'Users',
  'pagination'=> 15,
  'tools'=>['add','csv'],
  'commands'=>['edit'],
  'id'=>'id',
  'lang'=>'core/lang/admin/',
  'permissions'=>[
    'create'=>['admin','admin_user'],
    'read'=>['admin','admin_user'],
    'update'=>['admin','admin_user'],
    'delete'=>false
  ],
  'csv'=> ['id','username','email'],
  'fields'=> [
    'id'=> [
      'title'=>'ID',
      'edit'=>false
    ],
    'username'=> [
      'title'=>'Name',
      'qtype'=>'varchar(80)'
    ],
    'email'=> [
      'title'=>'Email',
      'qtype'=>'varchar(80)'
    ],
    'pass'=> [
      'list'=>false,
      'type'=>'password',
      'title'=>'Password',
      'qtype'=>'varchar(120)'
    ],
    'userrole'=> [
      'title'=>'Roles',
      'type'=>'meta',
      'edit'=>true,
      'mt'=>['usermeta', 'user_id', 'value'],
      'metatype'=>['vartype', 'role'],
      'options'=>[],
      'qoptions'=>'SELECT `id`,`userrole` FROM userrole;'
    ],
    'active'=> [
      'type'=>'checkbox',
      'title'=>'Active',
      'qtype'=>'INT(1) DEFAULT 1'
    ],
    'manager'=> [
      'type'=>'meta',
      'title'=>'Manager',
      'list'=>false,
      'mt'=>['usermeta', 'user_id', 'value'],
      'metatype'=>['vartype', 'manager_id'],
      'options'=>[''=>'-'],
      'qoptions'=>'SELECT `id`,`username` FROM user;'
    ]
  ],
  'events'=>[
    ['change',function(&$row){
      if(isset($row['pass'])) if( substr( $row['pass'], 0, 7 ) != "$2y$10$" )
        $row['pass'] = Gila::hash($row['pass']);
    }]
  ]
];
