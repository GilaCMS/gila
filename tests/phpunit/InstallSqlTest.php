<?php
include __DIR__.'/includes.php';
include_once __DIR__.'/../../src/core/classes/User.php';
use PHPUnit\Framework\TestCase;
use Gila\DB;

class InstallSqlTest extends TestCase
{
  public function test_installSql()
  {
    $_POST['adm_user'] = 'Admin';
    $_POST['adm_email'] = 'admin@example.com';
    $_POST['adm_pass'] = 'password';
    include 'src/core/install/install.sql.php';

    $tableColumn = [
      'post'=>['id','title','slug','description','user_id','language','publish','post','updated','created'],
      'postmeta'=>['id','post_id','vartype','value'],
      'page'=>['id','title','slug','description','template','language','publish','updated','blocks','image'],
      'user'=>['id','username','email','pass','active','created','updated','language'],
      'usermeta'=>['id','user_id','vartype','value'],
      'usergroup'=>['id','usergroup','description'],
      'userrole'=>['id','userrole','level','description'],
      'widget'=>['id','widget','title','area','pos','active','data','language'],
      'option'=>['option','value'],
      'postcategory'=>['id','title','slug','description'],
      'user_notification'=>['id','user_id','type','details','url','unread','created'],
      'sessions'=>['id','user_id','gsessionid','ip_address','user_agent','updated','data'],
      'menu'=>['id','menu','data'],
      'tableschema'=>['id','name','data'],
      'event_log'=>['id','created','type','user_id','data'],
      'redirect'=>['id','from_slug','to_slug','active']
    ];

    $tables = DB::get('SHOW TABLES');
    $this->assertEquals(count($tableColumn), count($tables));

    foreach ($tables as $table) {
      $tableName = $table[0];
      $columns = DB::get('DESCRIBE '.$tableName);
      foreach ($columns as $c=>$column) {
        $this->assertEquals($tableColumn[$tableName][$c], $column[0]);
      }
    }

    $this->assertEquals(1, DB::value('SELECT COUNT(*) FROM user'));
    $this->assertEquals(1, DB::value('SELECT COUNT(*) FROM userrole'));
    $this->assertEquals(4, DB::value('SELECT COUNT(*) FROM widget'));
    $this->assertEquals(1, DB::value('SELECT COUNT(*) FROM `page`'));
    $this->assertEquals(1, DB::value('SELECT COUNT(*) FROM `post`'));
    $json = DB::value('SELECT blocks FROM `page` WHERE id=1');
    $this->assertEquals(json_decode($json, true)[0]['_type'], 'text');
  }
}
