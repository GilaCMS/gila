<?php

return [
  'fields'=>[
    'heading'=>[],
    'subheading'=>[],
    'align'=>[
      'type'=>'select',
      'options'=>['left'=>'Left','center'=>'Center','right'=>'Right']
    ],
    'cards'=>[
      'type'=>'list',
      'fields'=>[
        'image'=>['type'=>'media'],
        'title'=>[],
        'text'=>[],
        'link_text'=>[],
        'link_url'=>[]
      ],
      'default'=>'[["assets/core/photo.png","Card","","",""],["assets/core/photo.png","Card","","",""],["assets/core/photo.png","Card","","",""]]'
    ]
  ],
  'keys'=>'page'
];
