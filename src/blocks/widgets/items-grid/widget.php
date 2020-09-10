<?php

return [
  'fields'=>[
    'items'=>[
      'type'=>'list',
      'fields'=>[
        'image'=>['type'=>'media','default'=>'src/core/assets/cogs.png'],
        'name'=>[],
        'text'=>[],
        'url'=>[],
      ],
      'default'=>'[["src/core/assets/cogs.png", "Feature"],["src/core/assets/cogs.png", "Feature"],["src/core/assets/cogs.png", "Feature"]]'
    ]
  ],
  'keys'=>'page'
];
