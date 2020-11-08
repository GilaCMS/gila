<?php

return [
  'fields'=>[
    'image'=> [
      "type"=>"media",
      "default"=>'$p=l1.jpg'
    ],
    'description'=> [],
    'caption'=> [
      "type"=>"paragraph"
    ]
  ],
  'keys'=>'page,widget'
];
