<?php

namespace Gila;

class TableSchema
{
  public function __construct($name, $initRows = [])
  {
    $gtable = new Table($name);
    $table = $gtable->getTable();
    // UPDATE/CREATE TABLE
    self::update($table, $initRows);
  }

  public static function update($table, $initRows = [])
  {
    $tname = $table['name'];
    $id = $table['id'] ?? 'id';

    // IF TABLE EXISTS
    $tables = DB::getList("SHOW TABLES;");
    $tableExists = in_array($tname, $tables);
    // CREATE TABLE
    $qtype = $table['fields'][$id]['qtype'] ?? 'INT NOT NULL AUTO_INCREMENT';
    $q = "CREATE TABLE IF NOT EXISTS $tname($id $qtype,PRIMARY KEY (`$id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    DB::query($q);

    // DESCRIBE
    $rows = DB::getRows("DESCRIBE $tname;");
    $dfields = [];
    foreach ($rows as $row) {
      $dfields[$row[0]] = [
        'type'=> $row[1],
        'null'=> $row[2],
        'key'=> $row[3]
      ];
    }

    // ADD COLUMNS
    foreach ($table['fields'] as $fkey=>$field) {
      if (isset($field['qtype']) && $fkey!=$id) {
        $qtype = $field['qtype'];
        $column = $field['qname']??$fkey;
        if (strpos($column, '(') !== false) {
          continue;
        }
        if (!isset($dfields[$fkey])) {
          $q = "ALTER TABLE $tname ADD `$column` {$qtype};";
          DB::query($q);
        } else {
          $_type = $dfields[$fkey]['type'];
          if ($_type != substr($field['qtype'], 0, strlen($_type))) {
            $q = "ALTER TABLE $tname MODIFY `$column` {$qtype};";
            DB::query($q);
          }
        }
      }
    }

    // ADD KEYS
    if (isset($table['qkeys'])) {
      foreach ($table['qkeys'] as $key) {
        if (empty($dfields[$key]['key'])) {
          $q = "ALTER TABLE $tname ADD KEY `$key` (`$key`);";
          DB::query($q);
        }
      }
    }

    // CREATE META TABLE
    if (isset($table['meta_table'])) {
      $m = $table['meta_table'];
      $q = "CREATE TABLE IF NOT EXISTS `{$m[0]}`(id INT NOT NULL AUTO_INCREMENT,
        `{$m[1]}` int(11) DEFAULT NULL,
        `{$m[2]}` varchar(80) DEFAULT NULL,
        `{$m[3]}` varchar(255) DEFAULT NULL,
        PRIMARY KEY (`id`),  KEY `{$m[1]}` (`{$m[1]}`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
      DB::query($q);
    }

    // INITIAL ROWS
    if ($tableExists === false) {
      foreach ($initRows as $row) {
        $gtable->createRow($row);
      }
    }
  }
}
