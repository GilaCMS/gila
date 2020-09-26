<?php

return [
  'fields'=>[
    'align'=>[
      'type'=>'select',
      'options'=>['left'=>'Left','center'=>'Center','right'=>'Right']
    ],
    'features'=>[
      'type'=>'list',
      'fields'=>[
        'image'=>['type'=>'media','default'=>'assets/core/photo.png'],
        'name'=>[],
        'text'=>[],
        'url'=>[],
      ],
      'default'=>'[["assets/core/photo.png", "Feature"],["assets/core/photo.png", "Feature"],["assets/core/photo.png", "Feature"]]'
    ]
  ],
  'keys'=>'page'
];
