<?php

return [
  'name'=> 'user',
  'title'=> 'Users',
  'pagination'=> 15,
  'tools'=>['add_popup','csv'],
  'commands'=>['edit_popup','delete'],
  'id'=>'id',
  'lang'=>'core/lang/admin/',
  'meta_table'=>['usermeta', 'user_id', 'vartype', 'value'],
  'js'=>['src/core/tables/user.js'],
  'permissions'=>[
    'read'=>['admin','admin_user'],
    'create'=>['admin','admin_user'],
    'update'=>['admin','admin_user'],
    'delete'=>['admin']
  ],
  'csv'=> ['id','username','email'],
  'search_box'=>true,
  'fields'=> [
    'id'=> [
      'title'=>'ID',
      'edit'=>false,
      'create'=>false
    ],
    'photo'=> [
      'type'=>'meta',
      'input_type'=>'media2',
      'title'=>'Photo',
      'meta_key'=>'photo',
      'create'=>false
    ],
    'username'=> [
      'title'=>'Name',
      'qtype'=>'varchar(80)',
      'required'=>true
    ],
    'email'=> [
      'title'=>'Email',
      'type'=>'email',
      'qtype'=>'varchar(80) UNIQUE',
      'required'=>true
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
      'input_type'=>'role',
      'edit'=>true,
      'meta_key'=>'role',
      'options'=>[],
      'qoptions'=>"SELECT `id`,`userrole` FROM userrole"
    ],
    'usergroup'=> [
      'title'=>'Groups',
      'type'=>'meta',
      'edit'=>true,
      'meta_key'=>'group',
      'options'=>[],
      'qoptions'=>["id","usergroup","usergroup"]
    ],
    'active'=> [
      'type'=>'checkbox',
      'title'=>'Active',
      'qtype'=>'INT(1) DEFAULT 1'
    ],
    'created'=> [
      'title'=>'Created',
      'type'=>'date',
      'list'=>false,
      'edit'=>false,
      'create'=>false,
      'qtype'=>'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP'
    ],
    'updated'=> [
      'title'=>'Updated',
      'type'=>'date',
      'list'=>false,
      'edit'=>false,
      'create'=>false,
      'qtype'=>'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
    ],
    'manager'=> [
      'type'=>'meta',
      'title'=>'Manager',
      'list'=>false,
      'meta_key'=>'manager_id',
      'options'=>[''=>'-'],
      'qoptions'=>'SELECT `id`,`username` FROM user;'
    ],
    'language'=> [
      'list'=>false,
      'edit'=>false,
      'create'=>false,
      'qtype'=>'VARCHAR(2)'
    ],
  ],
  'events'=>[
    ['create', function (&$row) {
      if (User::getByEmail($row['email'])) {
        Table::$error = __('Email already in use');
        $row = false;
      }
      if (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
        Table::$error = __('Wrong email format');
        $row = false;
      }
    }],
    ['change', function (&$row) {
      if (isset($row['userrole'])) {
        $roles = is_array($row['userrole'])? $row['userrole']: explode(',', $row['userrole']);
        $level = Gila\User::level(Gila\Session::userId());
        foreach ($roles as $roleId) {
          if ($level<Gila\User::roleLevel($roleId)) {
            http_response_code(500);
            exit;
          }
        }
      }
      if (isset($row['pass']) && !empty($row['pass'])) {
        if (substr($row['pass'], 0, 7) != "$2y$10$") {
          $row['pass'] = Config::hash($row['pass']);
        }
      }
    }]
  ]
];
