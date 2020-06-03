
<?php

include(__DIR__.'/includes.php');
include(__DIR__.'/../../src/core/classes/gTable.php');
include(__DIR__.'/../../src/core/classes/TableSchema.php');
use PHPUnit\Framework\TestCase;

class ClassGTable extends TestCase
{
  public function test_gTable()
  {
    global $db;
    Gila::content('post','core/tables/post.php');
    $gtable = new gTable('post');
    $this->assertEquals('post', $gtable->name());
    $this->assertEquals('id', $gtable->id());
    $this->assertEquals('Description', $gtable->fieldAttr('description', 'title'));
    $this->assertEquals(['id','title','slug','user_id','updated','publish','post'], $gtable->fields('csv'));
    $this->assertEquals(' LIMIT 30, 15', $gtable->limitPage(['page'=>3]));
    $fields = $gtable->getFields('csv');
    $this->assertEquals('select', $fields['user_id']['type']);

    $gtable->update();
    $db->query("INSERT INTO post(title, slug, user_id) VALUES('Post Tile', 'post1', 1);");
    $this->assertTrue($gtable->can('read'));
    $rows = $gtable->getRows(['slug'=>'post1'], ['select'=>['id','title']]);
    $this->assertEquals('Post Tile', $rows[0]['title']);
    $row = $gtable->getRow(['id'=>$rows[0]['id']], ['select'=>['id','title']]);
    $this->assertEquals('Post Tile', $row['title']);
  }

  public function test_where()
  {
    Gila::content('post','core/tables/post.php');
    $gtable = new gTable('post');

    $this->assertEquals(" WHERE id>10", $gtable->where(['id'=>['gt'=>10]]));
    $this->assertEquals(" WHERE id>=10", $gtable->where(['id'=>['ge'=>10]]));
    $this->assertEquals(" WHERE id<10", $gtable->where(['id'=>['lt'=>10]]));
    $this->assertEquals(" WHERE id<=10", $gtable->where(['id'=>['le'=>10]]));
    $this->assertEquals(" WHERE id>'10'", $gtable->where(['id'=>['gts'=>10]]));
    $this->assertEquals(" WHERE id<'10'", $gtable->where(['id'=>['lts'=>10]]));
    $this->assertEquals(" WHERE title like 'a%'", $gtable->where(['title'=>['begin'=>'a']]));
    $this->assertEquals(" WHERE title like '%x'", $gtable->where(['title'=>['end'=>'x']]));
    $this->assertEquals(" WHERE title like '%s%'", $gtable->where(['title'=>['has'=>'s']]));
    $this->assertEquals(" WHERE id IN(10,11)", $gtable->where(['id'=>['in'=>'10,11']]));
  }

  public function test_getMeta()
  {
    global $db;
    Gila::content('post','core/tables/post.php');
    $gtable = new gTable('post');

    $db->query("DELETE FROM postmeta WHERE vartype IN('fruit','color')");
    $db->query("INSERT INTO postmeta(post_id, vartype, `value`)
      VALUES(1,'fruit','orange'),(1,'fruit','apple'),(1,'color','red'),(2,'color','blue');");
    $this->assertEquals(['orange','apple'], $gtable->getMeta(1, 'fruit'));
    $this->assertEquals(['red'], $gtable->getMeta(1)['color']);
  }
}
