<?php

$table = [
    'name'=> 'user',
    'title'=> 'Users',
    'pagination'=> 15,
    'tools'=>['add','csv'],
    'commands'=>['edit'],
    'id'=>'id',
    'permissions'=>[
        'create'=>['admin','admin_user'],
        'read'=>['admin','admin_user'],
        'update'=>['admin','admin_user'],
        'delete'=>false
    ],
    'csv'=> ['id','username','email'],
    'fields'=> [
        'id'=> [
          "title"=>"ID",
          'edit'=>false
        ],
        'username'=> [
          "title"=>"Name"
        ],
        'email'=> [
          "title"=>"Email"
        ],
        'pass'=> ['list'=>false,'type'=>'password','title'=>'Password'],
        'userrole'=>[
            'title'=>"Roles",
            'type'=>'meta',
            'edit'=>true,
            "mt"=>['usermeta', 'user_id', 'value'],
            'metatype'=>['vartype', 'role'],
            'qoptions'=>'SELECT `id`,`userrole` FROM `userrole`;'
        ],
        "privileges"=>[
            //'list'=>false,
            'edit'=>true,
            'type'=>'meta',
            "mt"=>['usermeta', 'user_id', 'value'],
            'metatype'=>['vartype', 'privilege'],
            "title"=>"Privileges",
            "options"=>[]
        ]
    ],
    "oncreate"=>function(&$row){
        $row['pass'] = gila::hash($row['pass']);
    },
    "events"=>[
        ["change",function(&$row){
            if(isset($row['pass'])) if( substr( $row['pass'], 0, 7 ) != "$2y$10$" )
                $row['pass'] = gila::hash($row['pass']);
        }]
    ]
];

foreach(gila::$privilege as $k=>$p) {
    $table['fields']['privileges']['options'][$k] = ucfirst($k);
}
//
