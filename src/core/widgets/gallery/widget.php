<?php

return [
  'fields'=>[
    'columns'=>[
      'type'=>'radio',
      'options'=>[2=>2,3=>3,4=>4,5=>5],
      'default'=>3
    ],
    'images'=>[
      'type'=>'list',
      'fields'=>[
        'image'=>[
          'type'=>'media','default'=>'src/core/assets/photo.png'
        ],
        'caption'=>[]
      ],
      'default'=>'[["$p=l1.jpg"],["$p=l2.jpg"],["$p=l3.jpg"],["$p=p1.jpg"]]'
    ]
  ],
  'keys'=>'page,widget'
];
