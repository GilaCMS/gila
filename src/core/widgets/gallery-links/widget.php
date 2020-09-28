<?php

return [
  'fields'=>[
    'images'=>[
      'type'=>'list',
      'fields'=>[
        'image'=>[
          'type'=>'media', 'default'=>'assets/core/photo.png'
        ],
        'alt'=>[],
        'url'=>[]
      ],
      'default'=>'[]'
    ]
  ],
  'keys'=>'widget'
];
