<ul class="g-nav">
  <li><a href="admin/sql">Show Tables</a></li>
</ul>
<form action="<?=gila::url('admin/sql')?>" method="POST" id="qform">
  <textarea class="g-input" style="width:100%" name="query" id="query"><?=($q??'')?></textarea>
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
      $query = "SELECT * FROM `$v` LIMIT 30;";
      foreach($row as $key=>$v) echo '<td><button class="gb-btn" onclick="query.value=\''.$query.'\';qform.submit()">'.$v.'</button>';
    } else{
      foreach($row as $key=>$v) echo '<td>'.htmlentities($v);
    }
  }
  echo '</table>';
}
