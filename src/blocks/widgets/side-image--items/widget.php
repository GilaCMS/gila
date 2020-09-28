<?php

$item = '<h3>&#9745; Lorem Ipsum</h3>';

return [
  'fields'=>[
    'text'=>[
      'type'=>'paragraph',
      'allow_tags'=>true,
      'default'=>'<h2>Your Title</h2><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>'.$item.$item.$item
    ],
    'image'=>[
      'type'=>'media',
      'default'=>'assets/core/photo.png'
    ],
    'side'=>[
      'type'=>'radio',
      'options'=>['Left', 'Right']
    ]
  ],
  'keys'=>'page'
];
