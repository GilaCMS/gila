<?php


class db_backup {
	private $dir;

	function __construct()
	{
        $this->dir = 'log/db-backups/';
        if (!file_exists($this->dir)) mkdir($this->dir, 0755,true);

        if (isset($_POST['backup'])) $this->backup_tables("post,postmeta");
        if (isset($_GET['source'])) $this->source($_GET['source']);


        echo "<form action='admin/db_backup' method='post'><input type='hidden' name='backup' value='1'>
        <button class='g-btn' onclick='submit();'>Make a new backup</button></form>";

        echo "<br><br><br>";
        $files1 = scandir($this->dir);
        echo '<div class="row">';
        if (count($files1)>2) {
          echo "Backups found in /$this->dir:";
          echo '<table class="g-table table-hover"><tbody>';
          for($i=2;$i<count($files1);$i++) {
              echo '<tr><td>'.$files1[$i].'';
              echo '<td><a class="g-btn" href="uploads/'.$this->dir.$files1[$i].'"><i class="fa fa-download"></i> Download</a>';
              echo '<td><a class="g-btn" href="admin/db_backup?source='.$this->dir.$files1[$i].'"><i class="fa fa-upload"></i> Load</a>';
              echo '</tr>';
          }

          echo '</tbody></table>';

        } else echo "No backups found";
        echo '</div>';
    }

    /* backup the db OR just a table */
    function backup_tables($tables = '*') /*$host,$user,$pass,$name*/
    {
    	global $db;
        $tables = array();
        $return='';
        //exec("php /home/preactor/public_html/aps/pages/admin/db_backup2.php > /dev/null");

    	//get all of the tables
    	if ($tables == '*')
    	{
    		$result = $db->query('SHOW TABLES');
    		while($row = mysqli_fetch_row($result)) $tables[] = $row[0];
    	} else $tables = is_array($tables) ? $tables : explode(',',$tables);

    	$handle = fopen($this->dir.'db-'.date("Y-m-d").'.sql','w+');

    	//cycle through
    	foreach ($tables as $table)
    	{
        $return = '';
    	$result = $db->query('SELECT COUNT(*) FROM '.$table);
        if (!$result) continue;
        $num_rows = mysqli_fetch_row($result)[0];
    	fwrite($handle,'DROP TABLE '.$table.' IF EXISTS;');
    	$row2 = mysqli_fetch_row($db->query('SHOW CREATE TABLE '.$table));
    	fwrite($handle,"\n\n".$row2[1].";\n\n");

        $row_total = 0;
    	while ($num_rows>$row_total) {
            $row_inx = 0;
            $result=false;
            $result = $db->query('SELECT * FROM '.$table.' LIMIT '.$row_total.',100;');

			if($result) {
                $num_fields = mysqli_num_fields($result);
                while($row = mysqli_fetch_row($result))
                {
                    $row_total ++;
                    $row_inx ++;
                    if ($row_inx == 100) {
                        $row_inx = 1;
                        fwrite($handle,");\n");
                    }

                    if ($row_inx == 1) fwrite($handle,'INSERT INTO '.$table.' VALUES('); else fwrite($handle,'),(');

                    for ($j=0; $j < $num_fields; $j++) {
                        $row[$j] = addslashes($row[$j]);
                        $row[$j] = preg_replace("/\n/","\\n",$row[$j]);
                        if (isset($row[$j])) {
                            if (ctype_digit($row[$j])) {
                                fwrite($handle,$row[$j]);
                            } else fwrite($handle,'"'.$row[$j].'"');
                        } else { fwrite($handle,'""'); }
                        if ($j < ($num_fields-1)) {
                            fwrite($handle,',');
                        }
                    }
                }
                if ($row_inx > 0) fwrite($handle,");\n\n\n");
            }
    	}
    	}
    	//save file
    	fclose($handle);
    }

    function source($file) {
        global $db;
        $db->multi_query(file_get_contents($file));
        echo "Backup loaded successfully!";
    }
}
