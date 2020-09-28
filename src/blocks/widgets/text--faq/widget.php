<?php

$lorem = '<h3>Lorem Impsum</h3><p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>';

return [
  'fields'=>[
    'text'=>[
      'type'=>'paragraph',
      'allow_tags'=>true,
      'default'=>$lorem.$lorem.$lorem.$lorem
    ]
  ],
  'keys'=>'page'
];
