<?php


class DbBackup
{
  static private $dir;

  static function setDirectory($x)
  {
    self::$dir = $x;
  }

  static function getDirectory()
  {
    return self::$dir;
  }

  /**
  * Backup the whole database or just a table
  * @param $tables optional (Array) The tables to backup. Default: '*'(all)
  */
  static function backupTables($tables = '*')
  {
    global $db;
    $return = '';

    if ($tables === '*')
    {
      $tables = [];
      $result = $db->query('SHOW TABLES');
      while($row = mysqli_fetch_row($result)) $tables[] = $row[0];
    } else {
      $tables = is_array($tables) ? $tables : explode(',',$tables);
    }

    $handle = fopen(self::$dir.'db-'.date("Y-m-d").'.sql','w+');

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
        if($table==='option') {
          $result = $db->query('SELECT * FROM `option`
            WHERE `option` NOT LIKE "%_key"
            AND `option` NOT LIKE "%_token"
            AND `option` NOT LIKE "%Key"
            AND `option` NOT LIKE "%Token"
            LIMIT '.$row_n.',100000;');
            $row_n = $num_rows;
        } else {
          $result = $db->query('SELECT * FROM '.$table.' LIMIT '.$row_n.',100;');
        }

        if($result) {
          $fline = '';
          $n_fields = mysqli_num_fields($result);
          while($row = mysqli_fetch_row($result))
          {
            $row_n++;
            $row_inx++;

            if ($row_inx === 100) {
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
  static function source($file) {
    global $db;
    $file = basename($file);
    $db->multi_query(file_get_contents(self::$dir.$file));
    View::alert('success',"Backup loaded successfully!");
  }

  /**
  * Returns a source file for download
  * @param $file (string) Relative path of the file
  */
  static function download($file) {
    $file = basename($file);
    if(file_exists(self::$dir.$file)) {
      header("Content-Disposition:attachment;filename=$file");
      readfile(self::$dir.$file);
    } else {
      http_response_code(404);
    }
  }
}
