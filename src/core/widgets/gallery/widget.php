<?php

return [
  'fields'=>[
    'images'=>[
      'type'=>'list',
      'fields'=>[
        'image'=>[
          'type'=>'media','default'=>'src/core/assets/photo.png'
        ],
        'caption'=>[]
      ],
      'default'=>'[]'
    ]
  ],
  'keys'=>'page,widget'
];
