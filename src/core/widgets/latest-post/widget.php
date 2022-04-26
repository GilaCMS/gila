<?php

return [
  'fields'=>[
    'n_post'=>[
      'title'=>'Number of posts',
      'default'=>'5',
    ],
    'show_thumbnails'=>[
      'title'=>'Show thumbnails',
      'type'=>'select',
      'options'=>[0=>'No',1=>'Yes'],
      'default'=>0
    ]
  ],
  'keys'=>'widget,page'
];
