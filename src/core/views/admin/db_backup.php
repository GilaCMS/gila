<?php
view::alerts();
echo "<form action='admin/db_backup' method='post'>";
echo gForm::hiddenInput('db_backup');
echo "<button class='g-btn' onclick='submit();'>Make a new backup</button></form>";

echo "<br><br><br>";
$files1 = scandir($c->dir);
echo '<div class="row">';
if (count($files1)>2) {
  echo "Backups found in /$c->dir:";
  echo '<table class="g-table table-hover"><tbody>';
  for($i=2;$i<count($files1);$i++) {
      echo '<tr><td>'.$files1[$i].'';
      echo '<td><a class="g-btn" href="admin/db_backup?csrf='.$csrf.'&download='.$files1[$i].'"><i class="fa fa-download"></i> Download</a>';
      echo '<td><a class="g-btn" href="admin/db_backup?csrf='.$csrf.'&source='.$files1[$i].'"><i class="fa fa-upload"></i> Load</a>';
      echo '</tr>';
  }

  echo '</tbody></table>';

} else echo "No backups found";
echo '</div>';
