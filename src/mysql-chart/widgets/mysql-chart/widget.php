<?php

$q = [];
foreach (Config::getList('chartjs-query') as $item) {
  $q[] = $item['label'];
}
$dataOptions = array_merge($q, ['mysql'=>'Mysql Query', 'json'=>'JSON', 'csv'=>'CSV']);

return [
  'fields'=>[
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
      'title'=>'String', 'type'=>'textarea','allow-tags'=>true,
      'helptext'=>'A mysql query or data, <a target="_blank" href="https://gilacms.com/addons/package/mysql-chart">see examples</a>'
    ]
  ],
  'keys'=>'page,widget,chart'
];
