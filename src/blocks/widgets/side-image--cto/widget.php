<?php

$defaultData = <<<EOT
    <h2 class="section-heading">Your Title</h2>
    <p class="lead-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</p>
    <a href="#" class="btn btn-primary btn-lg">Download</a>&nbsp;
    <a href="#" class="btn btn-secondary btn-grey btn-lg">Learn more</a>
EOT;

return [
  'fields'=>[
    'text'=>[
      'type'=>'paragraph',
      'allow_tags'=>true,
      'default'=>$defaultData
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