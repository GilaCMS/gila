<?php

$dataOptions = ['web'=>'Page Views'];
foreach (Config::getList('chartjs-stat') as $item) {
  $dataOptions[$item['type']] = $item['label'];
}

return [
  'fields'=>[
    'legend'=>[
      'type'=>'select',
      'options'=>[
        ''=>'No','left'=>'Left','right'=>'Right','top'=>'Top','bottom'=>'Bottom',
      ]
    ],
    'data'=>[
      'type'=>'select',
      'options'=>$dataOptions
    ]
  ],
  'keys'=>'page,chart,widget'
];
