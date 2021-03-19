<?php

$widget_areas = ['x'=>'(None)'];
foreach (Gila\Config::$widget_area as $value) {
  $widget_areas[$value] = $value;
}
$widgets = [];
foreach (Gila\Widget::getList('widget') as $k=>$value) {
  $widgets[$k] = $k;
}

return [
    'name'=> 'widget',
    'title'=> 'Widgets',
    'id'=>'id',
    'tools'=>['add_popup'],
    'commands'=>['edit_widget','delete','clone'],
    'list'=> ['id','title','widget','area','pos','active'],
    'csv'=> ['id','title','widget','area','pos','active'],
    'lang'=>'core/lang/admin/',
    'js'=>['src/core/tables/widget.js'],
    'permissions'=>[
        'create'=>['admin'],
        'update'=>['admin'],
        'delete'=>['admin']
    ],
    'search_box'=> true,
    'search_boxes'=> ['area','widget'],
    'fields'=> [
        'id'=> ['title'=>'ID', 'edit'=>false, 'create'=>false],
        'widget'=> [
          'title'=>'Type', 'type'=>'select', 'options'=>$widgets, 'create'=>true,
          'qtype'=>'VARCHAR(80)'
        ],
        'title'=> [
          'title'=>'Title',
          'qtype'=>'VARCHAR(80)'
        ],
        'area'=> [
          'title'=>'Widget Area', 'type'=>'select', 'options'=>$widget_areas,
          'qtype'=>'VARCHAR(80)'
        ],
        'pos'=> [
          'title'=>'Position', 'default'=>1,
          'qtype'=>'INT(1) DEFAULT 0'
        ],
        'active'=> [
          'title'=>'Active',
          'type'=>'checkbox','edit'=>true,'create'=>false,
          'qtype'=>'INT(1) DEFAULT 1'
        ],
        'data'=> [
          'title'=>'Data', 'list'=>false, 'edit'=>false, 'create'=>false,
          'type'=>'text', 'allow_tags'=>true,
          'qtype'=>'TEXT'
        ],
        'language'=> [
          'list'=>false,'edit'=>false,'create'=>false,
          'qtype'=>'VARCHAR(2) DEFAULT NULL'
        ]
    ],
    'events'=>[
        ['change',
        function (&$row) {
          if (!isset($row['data']) || $row['data']!==null) {
            return;
          }
          $wdgt_options = Gila\Widget::getFields($row['widget']);

          $default_data=[];
          foreach ($wdgt_options as $key=>$op) {
            if (isset($op['default'])) {
              $def=$op['default'];
            } else {
              $def='';
            }
            $default_data[$key]=$def;
          }

          $row['data']=json_encode($default_data);
        }]
    ]
];
