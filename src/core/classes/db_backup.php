<?php


class db_backup
{
  private $dir;

  function __construct()
  {
    $this->dir = gila::dir('log/db-backups/');

    if (gForm::posted('db_backup')) {
      $this->backup_tables();
    }
    if (isset($_GET['csrf']) && gForm::verifyToken('db_backup2', $_GET['csrf'])) {
      if (isset($_GET['source'])) $this->source($_GET['source']);
      if (isset($_GET['download'])) {
        $this->download($_GET['download']);
        return;
      }
    }

    view::set('dir',$this->dir);
    view::set('csrf',gForm::getToken('db_backup2'));
    view::renderAdmin('admin/db_backup.php');
  }

  /**
  * Backup the whole database or just a table
  * @param $tables optional (Array) The tables to backup. Default: '*'(all)
  */
  function backup_tables($tables = '*')
  {
    global $db;
    $return = '';

    if ($tables == '*')
    {
      $tables = array();
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
      fwrite($handle, 'DROP TABLE IF EXISTS '.$table.';');
      $row2 = mysqli_fetch_row($db->query('SHOW CREATE TABLE '.$table));
      fwrite($handle, "\n\n".$row2[1].";\n\n");

      $row_n = 0;
      while ($num_rows>$row_n) {
        $row_inx = 0;
        $result=false;
        $result = $db->query('SELECT * FROM '.$table.' LIMIT '.$row_n.',100;');

        if($result) {
          $fline = '';
          $n_fields = mysqli_num_fields($result);
          while($row = mysqli_fetch_row($result))
          {
            $row_n++;
            $row_inx++;

            if ($row_inx == 100) {
              $row_inx = 1;
              $fline .= ");\n";
            }

            if ($row_inx == 1)
              $fline .= 'INSERT INTO '.$table.' VALUES(';
              else
              $fline .= '),(';

            for ($j=0; $j < $n_fields; $j++) {
              $row[$j] = addslashes($row[$j]);
              $row[$j] = preg_replace("/\n/","\\n",$row[$j]);
              if (isset($row[$j])) {
                if (ctype_digit($row[$j]))
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

  /**
  * Restore  database from a source file
  * @param $file (string) Relative path of the file
  */
  function source($file) {
    global $db;
    $file = basename($file);
    $db->multi_query(file_get_contents($this->dir.$file));
    view::alert('success',"Backup loaded successfully!");
  }

  /**
  * Returns a source file for download
  * @param $file (string) Relative path of the file
  */
  function download($file) {
    $file = basename($file);
    if(file_exists($this->dir.$file)) {
      header("Content-Disposition:attachment;filename='$file'");
      readfile($this->dir.$file);
    } else {
      http_response_code(404);
    }
  }
}
