<?php
// DEPRECATED
global $chartColors;
$chartColors = [
    'rgb(255, 99, 132)',
    'rgb(75, 192, 192)',
    'rgb(255, 159, 64)',
    'rgb(255, 205, 86)',
    'rgb(54, 162, 235)',
    'rgb(153, 102, 255)',
    'rgb(201, 203, 207)'
];
if ($palette = Config::get('admin_palette')) {
  $palettes = json_decode($palette, true);
  $chartColors = [];
  foreach ($palettes as $p) {
    $chartColors[] = 'rgb('.hexdec($p[1].$p[2]).','.hexdec($p[3].$p[4]).','.hexdec($p[5].$p[6]).')';
  }
  foreach ($palettes as $p) {
    $chartColors[] = 'rgba('.hexdec($p[1].$p[2]).','.hexdec($p[3].$p[4]).','.hexdec($p[5].$p[6]).',0.7)';
  }
}

if ($data['query']!='') {
  $labels = [];
  $datasets = [];
  $data = [];
  $label_data=[];
  $labelsN = 0;
  $datasetsN = 0;
  $data['datasets']=[];
  $dataArray = [];

  if ($data['data']=='mysql') {
      Gila\DB::query('SET SESSION TRANSACTION READ ONLY;');
      $res = Gila\DB::query($data['query']);
      Gila\DB::query->close();
    if ($res) {
      while ($r = mysqli_fetch_array($res)) {
        $dataArray[] = $r;
      }
    } elseif ($error = Gila\DB::error()) {
      echo "Mysql error: $error";
    }
  } elseif ($data['type']=='json') {
    return json_decode($data['query'], true);
  } else {
    $dataRows = explode("\n", $data['query']);
    foreach ($dataRows as $r) {
      $dataArray[] = str_getcsv($r);
    }
  }

  foreach ($dataArray as $r) {
    if (!in_array($r[0], $data['labels'])) {
      $data['labels'][]=$r[0];
    }
    if (!in_array($r[1], $datasets)) {
      $datasets[]=$r[1];
      $data['datasets'][] = [
        'label'=>$r[1],
        'borderWidth'=>2,
        'fill'=>false,
        'backgroundColor'=>$chartColors[$datasetsN],
        'borderColor'=>$chartColors[$datasetsN],
      ];
      $datasetsN++;
    }
    @$label_data[$r[0]][$r[1]] = $r[2];
  }

  foreach ($data['labels'] as $ilabel=>$label) {
    foreach ($data['datasets'] as $ids=>$ds) {
      if ($v = @$label_data[$label][$ds['label']]) {
        @$data['datasets'][$ids]['data'][$ilabel] = $v;
      } else {
        @$data['datasets'][$ids]['data'][$ilabel] = null;
      }
    }
  }

  return $data;
} else {
  return [
    'labels'=> ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
    'datasets'=> [
      ['label'=> 'Dataset 1','backgroundColor'=> 'rgb(255, 99, 132)','borderWidth'=> 0,
      'data'=> [11, 8, 16, 2, 15, 13, 10]],
      ['label'=> 'Dataset 2','backgroundColor'=> 'rgb(54, 162, 235)','borderWidth'=> 0,
      'data'=> [11, 13, 14, 20, 5, 9, 11]]
    ]
  ];
}
