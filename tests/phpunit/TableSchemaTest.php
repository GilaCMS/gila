
<?php

include(__DIR__.'/includes.php');
include(__DIR__.'/../../src/core/classes/Table.php');
include(__DIR__.'/../../src/core/classes/TableSchema.php');
use PHPUnit\Framework\TestCase;

class TableSchemaTest extends TestCase
{
  public function test_update()
  {
    global $db;
    TableSchema::update([
      'name'=> 'test_sc',
      'fields'=> [
        'col1'=> [
          'qtype'=> 'varchar(40)'
        ]
      ]
    ]);
    $describe = $db->getRows('DESCRIBE test_sc;');
    $this->assertEquals('id', $describe[0][0]);
    $this->assertEquals('varchar(40)', $describe[1][1]);

    TableSchema::update([
      'name'=> 'test_sc',
      'fields'=> [
        'col1'=> [
          'qtype'=> 'int'
        ]
      ]
    ]);
    $describe = $db->getRows('DESCRIBE test_sc;');
    $this->assertEquals('int', $describe[1][1]);
  }
}
