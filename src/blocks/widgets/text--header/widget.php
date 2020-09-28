<?php

$lorem = '<h3>Your title</h3><p class="subhead">Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi.</p>';

return [
  'fields'=>[
    'text'=>[
      'type'=>'paragraph',
      'allow_tags'=>true,
      'default'=>$lorem
    ]
  ],
  'keys'=>'page'
];
