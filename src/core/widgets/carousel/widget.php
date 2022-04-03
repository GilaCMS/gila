<?php
return [
  'fields'=>[
    'carousel-size'=>[
      'type'=>'range',
    ],
    'carousel-full-width'=>[
      'type'=>'radio',
      'options'=>[0=>'No',1=>'Yes'],
      'default'=>0
    ],
    'items'=>[
      'type'=>'list',
      'fields'=>[
        'image'=>[
          'type'=>'media','default'=>'assets/core/photo.png',
        ],
        'title (opcional)'=>[],
        'description (opcional)'=>[],
        'url (opcional)'=>[],
      ],
    ],
    'duration-in-seconds'=>[
      'type'=>'number',
      'default'=>3
    ],
    'button-title'=>[
      'type'=>'text',
    ],
    'color-text'=>[
      'type'=>'color',
    ],
    'button-font-size'=>[
      'type'=>'select',
      'options'=>['12'=>'Small','18'=>'Normal','25'=>'big']
    ],
    'text-align'=>[
      'type'=>'select',
      'options'=>['start'=>'Start','center'=>'Center','end'=>'End']
    ],
    'vertical-align'=>[
      'type'=>'range',
    ],
  ],
  'keys'=>'page,widget'
];
