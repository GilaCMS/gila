<?php

$table = [
    'name'=> 'user',
    'title'=> 'Users',
    'pagination'=> 15,
    'tools'=>['add','csv'],
    'commands'=>['edit'],
    'id'=>'id',
    'list'=> ['id','username','email','privileges'],
    'csv'=> ['id','username','email'],
    'permissions'=>[
        'create'=>['admin'],
        'update'=>['admin']
    ],
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
    "onchange"=>function(&$row){
        if( substr( $row['pass'], 0, 7 ) != "$2y$10$" )
            $row['pass'] = gila::hash($row['pass']);
    },
    "oncreate"=>function(&$row){
        $row['pass'] = gila::hash($row['pass']);
    }
];

foreach(gila::$privilege as $k=>$p) {
    $table['fields']['privileges']['options'][$k] = ucfirst($k);
}
//
