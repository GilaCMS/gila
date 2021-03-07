<?php
include __DIR__.'/includes.php';
include_once __DIR__.'/../../src/core/classes/User.php';
use PHPUnit\Framework\TestCase;

class InstallSqlTest extends TestCase
{
  public function test_installSql()
  {
    global $db;
    $_user='Admin';
    $_email='admin@mail.com';
    $_pass='password';
    include 'src/core/install/install.sql.php';

    $tableColumn = [
      'post'=>['id','title','slug','description','user_id','publish','post','updated','created'],
      'postmeta'=>['id','post_id','vartype','value'],
      'page'=>['id','title','slug','description','template','publish','updated','blocks'],
      'user'=>['id','username','email','pass','active','reset_code','created','updated'],
      'usermeta'=>['id','user_id','vartype','value'],
      'userrole'=>['id','userrole','level','description'],
      'widget'=>['id','widget','title','area','pos','active','data','language'],
      'option'=>['option','value'],
      'postcategory'=>['id','title','slug','description'],
      'user_notification'=>['id','user_id','type','details','url','unread','created'],
      'sessions'=>['id','user_id','gsessionid','ip_address','user_agent','updated','data'],
      'menu'=>['id','menu','data'],
      'tableschema'=>['id','name','data']
    ];

    $tables = $db->get('SHOW TABLES');
    $this->assertEquals(count($tableColumn), count($tables));

    foreach ($tables as $table) {
      $tableName = $table[0];
      $columns = $db->get('DESCRIBE '.$tableName);
      foreach ($columns as $c=>$column) {
        $this->assertEquals($tableColumn[$tableName][$c], $column[0]);
      }
    }

    $this->assertEquals(1, $db->value('SELECT COUNT(*) FROM user'));
    $this->assertEquals(1, $db->value('SELECT COUNT(*) FROM userrole'));
    $this->assertEquals(4, $db->value('SELECT COUNT(*) FROM widget'));
    $json = $db->value('SELECT blocks FROM `page` WHERE id=1');
    $this->assertEquals(json_decode($json, true)[0]['_type'], 'text');
  }
}
