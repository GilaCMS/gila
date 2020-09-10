<?php

$item = '<p>&#9745; Lorem Ipsum</p>';

return [
  'text'=>[
    'type'=>'paragraph',
    'allow_tags'=>true,
    'default'=>'<h2>Marine Life</h2><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>'.$item.$item.$item
  ],
  'image'=>[
    'type'=>'media',
    'default'=>'assets/core/photo.png'
  ],
  'side'=>[
    'type'=>'radio',
    'options'=>['Left', 'Right']
  ]
];
