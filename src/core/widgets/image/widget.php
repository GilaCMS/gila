<?php

return [
  'fields'=>[
    'image'=> [
      "type"=>"media2",
      "default"=>'$p=l1.jpg'
    ],
    'description'=> [],
    'caption'=> [
      "type"=>"paragraph"
    ]
  ],
  'keys'=>'page,widget'
];
