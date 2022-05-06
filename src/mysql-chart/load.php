<?php

include 'src/mysql-chart/queries.php';


Gila\Config::widgets([
  'mysql-chart'=>'mysql-chart/widgets/mysql-chart',
  'side-chart'=>'mysql-chart/widgets/side-chart',
  'stats-chart'=>'mysql-chart/widgets/stats-chart'
]);
