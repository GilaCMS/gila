<?php

namespace Gila;

class DbBackup
{
  private static $dir;

  public static function setDirectory($x)
  {
    self::$dir = $x;
  }

  public static function getDirectory()
  {
    return self::$dir;
  }

  /**
  * Backup the whole database or just a table
  * @param $tables optional (Array) The tables to backup. Default: '*'(all)
  */
  public static function backupTables($tables = '*')
  {
    global $db;
    $user = $GLOBALS['config']['db']['user'];
    $pass = $GLOBALS['config']['db']['pass'];
    $name = $GLOBALS['config']['db']['name'];
    $file = self::$dir.'db-'.date("Y-m-d").'.sql';
    $where = 'WHERE `option` LIKE "%_key" OR `option` LIKE "%_token"';
    $where .= ' OR `option` LIKE "%Key" OR `option` LIKE "%Token"';
    // read values
    $tokens = $db->get("SELECT * FROM `option` $where;");
    $db->query("UPDATE `option` SET `value`='****' $where;");
    $c = "mysqldump -u$user -p$pass $name > $file";
    shell_exec($c);
    // restore values
    foreach($tokens as $r) {
      $db->query("UPDATE `option` SET `value`=? WHERE `option`=?;", [$r['value'], $r['option']]);
    }
  }

  /**
  * Restore  database from a source file
  * @param $file (string) Relative path of the file
  */
  public static function source($file)
  {
    global $db;
    $file = basename($file);
    $db->multi_query(file_get_contents(self::$dir.$file));
    View::alert('success', "Backup loaded successfully!");
  }

  /**
  * Returns a source file for download
  * @param $file (string) Relative path of the file
  */
  public static function download($file)
  {
    $file = basename($file);
    if (file_exists(self::$dir.$file)) {
      header("Content-Disposition:attachment;filename=$file");
      readfile(self::$dir.$file);
    } else {
      http_response_code(404);
    }
  }
}
