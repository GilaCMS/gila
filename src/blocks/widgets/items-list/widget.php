<?php

return [
    'items'=>[
        'type'=>'list',
        'fields'=>[
            'image'=>['type'=>'media','default'=>'assets/core/check.png'],
            'name'=>[],
            'text'=>[],
            'url'=>[],
        ],
        'default'=>'[["assets/core/check.png", "Feature"],["assets/core/check.png", "Feature"],["assets/core/check.png", "Feature"]]'
    ],
    'image'=>[
        'type'=>'media'
    ]
];
