<?php

$table = [
    'name'=> 'user',
    'title'=> 'Users',
    'pagination'=> 15,
    'tools'=>['add','csv'],
    'commands'=>['edit'],
    'id'=>'id',
    'csv'=> ['id','name','email'],
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
        'pass'=> ['list'=>false,'type'=>'password'],
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
    "onupdate"=>function(&$registry_row){
        //$registry_row['Password'] = password_hash($registry_row['Password'], PASSWORD_BCRYPT);
    },
    "oncreate"=>function(&$registry_row){
        //$registry_row['Password'] = password_hash($registry_row['Password'], PASSWORD_BCRYPT);
    }
];

foreach(gila::$privilege as $k=>$p) {
    $table['fields']['privileges']['options'][$k] = ucfirst($k);
}
//
