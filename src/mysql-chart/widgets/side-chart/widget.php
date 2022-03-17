<?php

$q = [];
foreach (Config::getList('chartjs-query') as $item) {
  $q[] = $item['label'];
}
$dataOptions = array_merge($q, ['mysql'=>'Mysql Query', 'json'=>'JSON', 'csv'=>'CSV']);

return [
  'fields'=>[
    'text'=>[
      'type'=>'paragraph',
      'allow_tags'=>true,
      'default'=>'<h2>Your Title</h2><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>'
    ],
    'type'=>[
      'type'=>'select',
      'options'=>[
        'bar'=>'Bar (Label,Dataset,Data)',
        'bar.horizontal'=>'Bar Horizontal (Label,Dataset,Data)',
        'bar.stacked'=>'Bar Stacked (Label,Dataset,Data)',
        'line'=>'Line (Label,Dataset,Data)',
        'pie'=>'Pie (Label,Dataset,Data)',
        'radar'=>'Radar (Label,Dataset,Data)'
      ]
    ],
    'side'=>[
      'type'=>'radio',
      'options'=>['Left', 'Right']
    ],
    'legend'=>[
      'type'=>'select',
      'options'=>[
        ''=>'No','left'=>'Left','right'=>'Right','top'=>'Top','bottom'=>'Bottom',
      ]
    ],
    'data'=>[
      'type'=>'select',
      'options'=>$dataOptions
    ],
    'query'=>[
      'title'=>'String',
      'type'=>'textarea',
      'allow-tags'=>true,
      'helptext'=>'A mysql query or data, <a target="_blank" href="https://gilacms.com/addons/package/mysql-chart">see examples</a>'
    ]
  ],
  'keys'=>'page,chart,widget'
];
