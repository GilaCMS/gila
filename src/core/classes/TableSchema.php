<?php

class TableSchema
{

  function __construct ($name)
  {
    $gtable = new gTable($name);
    $table = $gtable->getTable();
    self::update($table);
  }

  static function update($table) {
    global $db;
    $tname = $table['name'];
    $id = $table['id'] ?? 'id';

    // CREATE TABLE
    $qtype = @$table['fields'][$id]['qtype']?:'INT NOT NULL AUTO_INCREMENT';
    $q = "CREATE TABLE IF NOT EXISTS $tname($id $qtype,PRIMARY KEY (`$id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    $db->query($q);

    // ADD COLUMNS
    foreach($table['fields'] as $fkey=>$field) {
      if(isset($field['qtype']) && $fkey!=$id) {
        $column = @$field['qcolumn']?:$fkey;
        if (strpos($column, '(') === false) {
          $q = "ALTER TABLE $tname ADD $column {$field['qtype']};";
          $db->query($q);
        }
      }
    }

    // ADD KEYS
    if(isset($table['qkeys'])) foreach($table['qkeys'] as $key) {
      $q = "ALTER TABLE $tname ADD KEY `$key` (`$key`);";
      $db->query($q);
    }
  }

}
