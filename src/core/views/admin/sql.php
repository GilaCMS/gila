<ul class="g-nav">
  <li><a href="admin/sql?query=SHOW TABLES">Show Tables</a></li>
</ul>
<form action="<?=gila::url('admin/sql')?>" method="POST">
  <textarea class="g-input" style="width:100%" name="query"><?=($q??'')?></textarea>
  <p><button class="g-btn" type="submit"><?=__('Execute')?></button></p>
</form><br>

<?php
global $db;

if(!isset($q)) $q='SHOW TABLES';
$res = $db->getAssoc($q);
if($res) {
  echo '<div style="width:100%;overflow-x:scroll"><table class="g-table">';
  foreach($res as $row) {
    if(!isset($thead)) {
        echo '<tr>';
        foreach($row as $key=>$v) echo '<th>'.$key;
        $thead=true;
    }
    echo '<tr>';
    if($q=="SHOW TABLES") {
      foreach($row as $key=>$v) echo '<td><a href="admin/sql?query=SELECT * FROM `'.$v.'` LIMIT 30;">'.$v.'</a>';
    } else{
      foreach($row as $key=>$v) echo '<td>'.$v;
    }
  }
  echo '</table>';
}
