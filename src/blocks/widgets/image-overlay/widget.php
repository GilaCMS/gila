<?php

$defaultData = <<<EOT
    <h2 class="section-heading">Your Title</h2>
    <p class="lead-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</p>
    <a href="#" class="btn btn-primary btn-lg">Download</a>&nbsp;
    <a href="#" class="btn btn-secondary btn-grey btn-lg">Learn more</a>
EOT;

return [
  'fields'=>[
    'image'=>[
      'type'=>'media',
      'default'=>'assets/core/photo.png'
    ],
    'text'=>[
      'type'=>'paragraph',
      'allow_tags'=>true,
      'default'=>$defaultData
    ],
    'height'=>[
      'default'=>'400px'
    ],
    'text_size'=>[
      'type'=>'select',
      'options'=>['100%'=>'100%','120%'=>'120%','140%'=>'140%'],
    ],
    'align'=>[
      'type'=>'radio',
      'options'=>['left'=>'Left','center'=>'Center','right'=>'Right'],
      'default'=>'center'
    ]
  ],
  'keys'=>'page,widget'
];
