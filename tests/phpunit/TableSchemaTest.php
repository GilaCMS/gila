
<?php

include __DIR__.'/includes.php';
include __DIR__.'/../../src/core/classes/Table.php';
include __DIR__.'/../../src/core/classes/TableSchema.php';
use PHPUnit\Framework\TestCase;
use Gila\TableSchema;

class TableSchemaTest extends TestCase
{
  public function test_update()
  {
    global $db;
    $db->query('DROP TABLE test_sc;');

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
    $this->assertEquals(null, $describe[1][3]);

    TableSchema::update([
      'name'=> 'test_sc',
      'qkeys'=> ['col1'],
      'fields'=> [
        'col1'=> [
          'qtype'=> 'int(4)'
        ]
      ]
    ]);
    $describe = $db->getRows('DESCRIBE test_sc;');
    $this->assertEquals('int(4)', $describe[1][1]);
    $this->assertEquals('MUL', $describe[1][3]);
  }
}
