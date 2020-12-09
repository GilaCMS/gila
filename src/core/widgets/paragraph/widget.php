<?php

$lorem = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

return [
  'fields'=>[
    'text'=>[
      'type'=>'paragraph',
      'allow_tags'=>true,
      'default'=>$lorem.' '.$lorem
    ]
  ],
  'keys'=>'removed'
];
