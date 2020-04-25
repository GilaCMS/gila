
<?php
chdir(__DIR__.'/../../');
include __DIR__.'/../../vendor/autoload.php';
include __DIR__.'/../../src/core/classes/gTable.php';
include __DIR__.'/../../src/core/classes/gila.php';
include __DIR__.'/../../src/core/classes/Db.php';
define("LOG_PATH", "log");
define("CONFIG_PHP", "config.php");
use PHPUnit\Framework\TestCase;

$db = new Db("127.0.0.1", "g_user", "password", "g_db");

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
    $this->assertEquals(" WHERE id IN(10,11)", $gtable->where(['id'=>['lts'=>'10,11']]));
  }
}
