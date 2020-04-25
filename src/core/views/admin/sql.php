<ul class="g-nav">
  <li><a href="admin/sql">Show Tables</a></li>
</ul>
<form action="<?=Gila::url('admin/sql')?>" method="POST" id="qform">
  <textarea class="g-input" style="width:100%" name="query" id="query"><?=($q??'')?></textarea>
  <p><button class="g-btn" type="submit"><?=__('Execute')?></button></p>
</form><br>

<?php
global $db;

if(!isset($q)) $q='SHOW TABLES';
echo '<div style="width:100%;overflow-x:scroll"><table class="g-table">';

if($q=="SHOW TABLES") {
  $res = $db->getRows($q);
  foreach($res as $row) {
    $v = $row[0];
    $orderby = "";
    if(isset(Gila::$content[$v])) {
      $table = new gTable($v);
      if($id = $table->getTable()['id']) {
        $orderby = "ORDER BY $id DESC";
      }
    }
    $query = "SELECT * FROM `$v` $orderby LIMIT 30;";
    echo '<tr><td><button class="gb-btn" onclick="query.value=\''.$query.'\';qform.submit()">'.$v.'</button>';
  }
}
else {
  $res = $db->getAssoc($q);
  foreach($res as $row) {
    if(!isset($thead)) {
      echo '<tr>';
      foreach($row as $key=>$v) echo '<th>'.$key;
      $thead=true;
    }
    echo '<tr>';
    foreach($row as $key=>$v) echo '<td>'.htmlentities($v);
  }
}

echo '</table>';
