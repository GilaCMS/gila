<?php

namespace Gila;

class TableSchema
{

  function __construct ($name)
  {
    $gtable = new Table($name);
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

    // CREATE META TABLE
    if(isset($table['meta_table'])) {
      $m = $table['meta_table'];
      $q = "CREATE TABLE IF NOT EXISTS `{$m[0]}`(id INT NOT NULL AUTO_INCREMENT,
        `{$m[1]}` int(11) DEFAULT NULL,
        `{$m[2]}` varchar(80) DEFAULT NULL,
        `{$m[3]}` varchar(255) DEFAULT NULL,
        PRIMARY KEY (`id`),  KEY `{$m[1]}` (`{$m[1]}`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
      $db->query($q);
    }
  }

}

class_alias('Gila\\TableSchema', 'TableSchema');
