<?php

return [
  'fields'=>[
    'items'=>[
      'type'=>'list',
      'fields'=>[
        'image'=>['type'=>'media','default'=>'assets/core/photo.png'],
        'name'=>[],
        'text'=>[],
        'url'=>[],
      ],
      'default'=>'[["assets/core/photo.png", "Feature", "Lorem ipsum dolor sit." ],["assets/core/photo.png", "Feature", "Lorem ipsum dolor sit."],["assets/core/photo.png", "Feature", "Lorem ipsum dolor sit."]]'
    ]
  ],
  'keys'=>'page'
];
