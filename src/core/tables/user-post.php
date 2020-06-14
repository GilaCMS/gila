<?php

return [
  'extends'=> 'core/tables/post.php',
  'csv'=> ['id','title','slug','updated','publish','post'],
  'permissions'=>[
    'read'=>['writer'],
    'create'=>['writer'],
    'update'=>['writer'],
    'delete'=>['writer']
  ],
  'filter_owner'=> 'user_id',
  'events'=>[
    ['create', function(&$row) {
      $row['user_id']=Session::userId();
    }]
  ]
];
