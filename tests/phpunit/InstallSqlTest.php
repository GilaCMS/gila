<?php
include(__DIR__.'/includes.php');
use PHPUnit\Framework\TestCase;

class InstallSqlTest extends TestCase
{
	public function test_installSql()
	{
		$link = &$db->link;
		$_user='Admin';
		$_email='admin@mail.com';
		$_pass='password';
		include('src/core/install/install.sql.php');

		$tables = $db->get('SHOW TABLES');
		$this->assertEquals(9, count($tables));

		$tableColumn = [
			'post'=>['id','user_id','title','slug','description','post','publish','created','updated'],
			'postmeta'=>['id','post_id','vartype','value'],
			'page'=>['id','title','slug','content','publish','template','updated'],
			'user'=>['id','username','email','pass','active','reset_code','created','updated'],
			'usermeta'=>['id','user_id','vartype','value'],
			'userrole'=>['id','userrole'],
			'widget'=>['id','widget','title','area','active','pos','data'],
			'option'=>['option','value'],
			'postcategory'=>['id','title','slug','description']	
		];		
		foreach($tables as $table) {
			$columns = $db->get('DESCRIBE '.$table);
			foreach($columns as $c=>$column) {
				$this->assertEquals($tableColumn[$table][$c], $column);
			}
		}

		$this->assertEquals(1, $db->value('SELECT COUNT(*) FROM user'));
		$this->assertEquals(1, $db->value('SELECT COUNT(*) FROM userrole'));
		$this->assertEquals(3, $db->value('SELECT COUNT(*) FROM widget'));
  }
}
