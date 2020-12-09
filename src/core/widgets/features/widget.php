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
        'image'=>['type'=>'media','default'=>'$p=l1.jpg'],
        'name'=>[],
        'text'=>[],
        'url'=>[],
      ],
      'default'=>'[["$p=l1.jpg", "Feature"],["$p=l1.jpg", "Feature"],["$p=l1.jpg", "Feature"]]'
    ]
  ],
  'keys'=>'page'
];
