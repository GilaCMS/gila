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
        'id'=> ['edit'=>false],
        'name'=> [],
        'email'=> [],
        'pass'=> ['list'=>false],
        "privileges"=>[
            //'list'=>false,
            'type'=>'meta',
            "mt"=>['usermeta', 'user_id', 'value'],
            'metatype'=>['vartype', 'privilege'],
            "title"=>"Privileges"
        ]
    ],
    "onupdate"=>function(&$registry_row){
        //$registry_row['Password'] = password_hash($registry_row['Password'], PASSWORD_BCRYPT);
    },
    "oncreate"=>function(&$registry_row){
        //$registry_row['Password'] = password_hash($registry_row['Password'], PASSWORD_BCRYPT);
    }
];
