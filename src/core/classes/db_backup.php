<?php


class db_backup {
	private $dir;

	function __construct()
	{
        $this->dir = 'log/db-backups/';
        if (!file_exists($this->dir)) mkdir($this->dir, 0755,true);

        if (isset($_POST['backup'])) $this->backup_tables("post,postmeta");
		if (isset($_GET['source'])) $this->source($_GET['source']);
		if (isset($_GET['download'])) {
			$this->download($_GET['download']);
			return;
		}

		view::set('dir',$this->dir);
		view::renderAdmin('admin/db_backup.php');

    }

    /* backup the db OR just a table */
    function backup_tables($tables = '*')
    {
    	global $db;
        $tables = array();
        $return='';

    	if ($tables == '*')
    	{
    		$result = $db->query('SHOW TABLES');
    		while($row = mysqli_fetch_row($result)) $tables[] = $row[0];
    	} else {
			$tables = is_array($tables) ? $tables : explode(',',$tables);
		}

    	$handle = fopen($this->dir.'db-'.date("Y-m-d").'.sql','w+');

    	foreach ($tables as $table)
    	{
        	$return = '';
    		$result = $db->query('SELECT COUNT(*) FROM '.$table);
        	if (!$result) continue;

        	$num_rows = mysqli_fetch_row($result)[0];
    		fwrite($handle, 'DROP TABLE '.$table.' IF EXISTS;');
    		$row2 = mysqli_fetch_row($db->query('SHOW CREATE TABLE '.$table));
    		fwrite($handle, "\n\n".$row2[1].";\n\n");

        	$row_n = 0;
    		while ($num_rows>$row_n) {
            	$row_inx = 0;
            	$result=false;
            	$result = $db->query('SELECT * FROM '.$table.' LIMIT '.$row_n.',100;');

				if($result) {
                	$n_fields = mysqli_num_fields($result);
                	while($row = mysqli_fetch_row($result))
                	{
                    	$row_n++;
                    	$row_inx++;
						$fline = "";

                    	if ($row_inx == 100) {
                        	$row_inx = 1;
                        	$fline = ");\n";
                    	}

                    	if ($row_inx == 1)
							$fline .= 'INSERT INTO '.$table.' VALUES(';
							else
							$fline .= '),(';

                    	for ($j=0; $j < $n_fields; $j++) {
                        	$row[$j] = addslashes($row[$j]);
                        	$row[$j] = preg_replace("/\n/","\\n",$row[$j]);
                        	if (isset($row[$j])) {
                            	if (ctype_digit($row[$j])) {
                                	$fline .= $row[$j];
                            	else
									$fline .= '"'.$row[$j].'"';
                        	} else {
								$fline .= '""';
							}
                        	if ($j < $n_fields-1) {
                            	$fline .= ',';
                        	}
                    	}
                	}
                	if ($row_inx > 0) $fline .= ");\n\n\n";
					fwrite($handle, $fline);
            	}
    		}
    	}

    	fclose($handle);
    }

    function source($file) {
        global $db;
        $db->multi_query(file_get_contents($file));
        echo "Backup loaded successfully!";
    }

	function download($file) {
		header("Content-Disposition:attachment;filename='$file'");
		readfile($file);
    }
}
