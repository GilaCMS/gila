<?php

return [
  'fields'=>[
    "image"=>[
      "type"=>"media",
      "default"=>"assets/core/photo.png"
    ],
    "heading"=>[
      "default"=>"Heading Text"
    ],
    "text"=>[
      "default"=>"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua"
    ],
    "primary_link"=>[
      "default"=>"#"
    ],
    "primary_text"=>[
      "default"=>"Download"
    ],
    "secondary_link"=>[
      "default"=>"#"
    ],
    "secondary_text"=>[
      "default"=>"Learn more"
    ],
    'side'=>[
      'type'=>'radio',
      'options'=>['Left', 'Right']
    ]
  ],
  'keys'=>'removed'
];
