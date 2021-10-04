
<?php

include __DIR__.'/includes.php';
include __DIR__.'/../../src/core/classes/Table.php';
include __DIR__.'/../../src/core/classes/TableSchema.php';
use PHPUnit\Framework\TestCase;

class TableTest extends TestCase
{
  public function test_Table()
  {
    global $db;
    Gila\Config::content('post', 'core/tables/post.php');
    Gila\Config::content('postcategory', 'core/tables/postcategory.php');
    $Table = new Gila\Table('post');
    $this->assertEquals('post', $Table->name());
    $this->assertEquals('id', $Table->id());
    $this->assertEquals('Description', $Table->fieldAttr('description', 'title'));
    $this->assertEquals(['id','title','slug','user_id','updated','publish','post'], $Table->fields('csv'));
    $this->assertEquals(' LIMIT 30, 15', $Table->limitPage(['page'=>3]));
    $fields = $Table->getFields('csv');
    $this->assertEquals('select', $fields['user_id']['type']);

    $Table->update();
    $db->query("INSERT INTO post(title, slug, user_id) VALUES('Post Tile', 'post1', 1);");
    $this->assertTrue($Table->can('read'));
    $rows = $Table->getRows(['slug'=>'post1'], ['select'=>['id','title']]);
    $this->assertEquals('Post Tile', $rows[0]['title']);
    $row = $Table->getRow(['id'=>$rows[0]['id']], ['select'=>['id','title']]);
    $this->assertEquals('Post Tile', $row['title']);
  }

  public function test_where()
  {
    Gila\Config::content('post', 'core/tables/post.php');
    Gila\Config::content('postcategory', 'core/tables/postcategory.php');
    $Table = new Gila\Table('post');

    $this->assertEquals(" WHERE `id`>10", $Table->where(['id'=>['gt'=>10]]));
    $this->assertEquals(" WHERE `id`>=10", $Table->where(['id'=>['ge'=>10]]));
    $this->assertEquals(" WHERE `id`<10", $Table->where(['id'=>['lt'=>10]]));
    $this->assertEquals(" WHERE `id`<=10", $Table->where(['id'=>['le'=>10]]));
    $this->assertEquals(" WHERE `id`>'10'", $Table->where(['id'=>['gts'=>10]]));
    $this->assertEquals(" WHERE `id`<'10'", $Table->where(['id'=>['lts'=>10]]));
    $this->assertEquals(" WHERE `title` like 'a%'", $Table->where(['title'=>['begin'=>'a']]));
    $this->assertEquals(" WHERE `title` like '%x'", $Table->where(['title'=>['end'=>'x']]));
    $this->assertEquals(" WHERE `title` like '%s%'", $Table->where(['title'=>['has'=>'s']]));
    $this->assertEquals(" WHERE `id` IN(10,11)", $Table->where(['id'=>['in'=>'10,11']]));
  }

  public function test_getMeta()
  {
    global $db;
    Gila\Config::content('post', 'core/tables/post.php');
    Gila\Config::content('postcategory', 'core/tables/postcategory.php');
    $Table = new Gila\Table('post');

    $db->query("DELETE FROM postmeta WHERE vartype IN('fruit','color')");
    $db->query("INSERT INTO postmeta(post_id, vartype, `value`)
      VALUES(1,'fruit','orange'),(1,'fruit','apple'),(1,'color','red'),(2,'color','blue');");
    $this->assertEquals(['orange','apple'], $Table->getMeta(1, 'fruit'));
    $this->assertEquals(['red'], $Table->getMeta(1)['color']);
  }

  public function test_purifyHtml()
  {
    global $db;
    Gila\Config::content('post', 'core/tables/post.php');
    Gila\Config::content('postcategory', 'core/tables/postcategory.php');
    $Table = new Gila\Table('post');
    $data = ['post'=>'<p><script>alert("xss")</script>Post</p>','slug'=>'post1'];

    $db->query('REPLACE INTO post SET id=1;');
    $db->query('UPDATE post SET post=\'\' WHERE id=1;');
    $res = $db->query("UPDATE {$Table->name()}{$Table->set($data)} WHERE {$Table->id()}=1;");
    $post = $db->value('SELECT post from post WHERE id=1;');
    $this->assertEquals('<p>Post</p>', $post);
  }

  public function test_extend_recursive()
  {
    $table1 = [
      'name'=>'table',
      'title'=>'Table1',
      'commands'=>['add','edit']
    ];
    $table2 = [
      'name'=>'table',
      'extends'=>'package/table/table.php',
      'title'=>'Table2',
      'commands'=>['delete']
    ];
    $table3 = [
      'name'=>'table',
      'extends'=>'package/table/table.php',
      'title'=>'Table2',
      'commands'=>['add','edit','delete']
    ];      
    $tableExtended = Gila\Table::extend_recursive($table1, $table2);
    $this->assertEquals($table3, $tableExtended);
  }
}
