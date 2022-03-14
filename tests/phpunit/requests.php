<?php

include __DIR__.'/includes.php';
include __DIR__.'/../../src/core/classes/Controller.php';
include __DIR__.'/../../src/core/classes/Package.php';
include __DIR__.'/../../src/core/classes/User.php';
include __DIR__.'/../../src/core/classes/Table.php';
include __DIR__.'/../../src/core/classes/TableSchema.php';
include __DIR__.'/../../src/core/classes/Form.php';
include __DIR__.'/../../src/core/classes/Sendmail.php';
include __DIR__.'/../../src/core/classes/HtmlInput.php';
use PHPUnit\Framework\TestCase;
use Gila\Config;
use Gila\Event;
use Gila\Router;
use Gila\Controller;
use Gila\Session;
use Gila\Package;
use Gila\Table;
use Gila\TableSchema;

$GLOBALS['config']['db']['name'] = 'g_db';
Event::listen('sendmail', function ($x) {
  return true;
});

class RequestsTest extends TestCase
{
  protected static $userId;

  public static function setUpBeforeClass()
  {
    global $db;
    self::createUserTable();
    $pass = Config::hash("password");
    $db->query(
      "INSERT INTO user SET email=?, pass=?, active=1;",
      ["test_login_auth@email.com", Config::hash("password")]
    );
    $uid = $db->insert_id;
    $db->query("INSERT INTO usermeta SET `value`='ABC', user_id=?, `vartype`='token';", [$uid]);
    $db->query("INSERT INTO usermeta SET `value`=1, user_id=?, `vartype`='role';", [$uid]);
    self::$userId = $uid;
  }

  public static function tearDownAfterClass()
  {
    global $db;
    $db->query("DELETE FROM user WHERE email='test_login_auth@email.com';");
    $db->query("DELETE FROM usermeta WHERE user_id=?;", self::$userId);
  }

  public function test_login_auth()
  {
    $_POST['email'] = "test_login_auth@email.com";
    $_POST['password'] = "password";
    Router::controller('user', 'core/controllers/UserController');
    $response = $this->request('user/auth', 'POST');
    $this->assertEquals('{"token":"ABC"}', $response);
  }

  public function test_register()
  {
    global $db;
    Router::controller('user', 'core/controllers/UserController');
    Session::user(0);
    $email = "test_register@email.com";
    $_REQUEST['email'] = $email;
    $_REQUEST['name'] = "Register Test";
    $_POST['email'] = $email;
    $_POST['name'] = "Register Test";
    $_POST['password'] = "pass";
    $GLOBALS['config']['user_activation'] = 'byadmin';
    $GLOBALS['config']['user_register'] = 0;
    $db->query('DELETE FROM user WHERE email=?;', $email);

    $this->request('user/register', 'POST');
    $uid = $db->value('SELECT id from user WHERE email=?;', $email);
    $this->assertNull($uid);

    $GLOBALS['config']['user_register'] = 1;
    $this->request('user/register', 'POST');
    $active = $db->value('SELECT active FROM user WHERE email=?;', $email);
    $this->assertEquals(0, $active);
    $db->query('DELETE FROM user WHERE email=?;', $email);

    $_REQUEST['name'] = "Register Test<script>alert(0)</script>";
    $_POST['name'] = "Register Test<script>alert(0)</script>";
    $this->request('user/register', 'POST');
    $uid = $db->value('SELECT id from user WHERE email=?;', $email);
    $this->assertNull($uid);

    $_REQUEST['name'] = "Register Test";
    $_POST['name'] = "Register Test";
    $GLOBALS['config']['user_activation'] = 'auto';
    $this->request('user/register', 'POST');
    $active = $db->value('SELECT active FROM user WHERE email=?;', $email);
    $this->assertEquals(1, $active);
    $db->query('DELETE FROM user WHERE email=?;', $email);

    $GLOBALS['config']['user_activation'] = 'byemail';
    $this->request('user/register', 'POST');
    $uid = $db->value('SELECT id FROM user WHERE email=?;', $email);
    $active = $db->value('SELECT active FROM user WHERE id=?;', $uid);
    $this->assertEquals(0, $active);

    $_GET['ap'] = 'wrongcode';
    $this->request('user/activate', 'GET');
    $active = $db->value('SELECT active FROM user WHERE id=?;', $uid);
    $this->assertEquals(0, $active);

    $_GET['ap'] = $db->value('SELECT `value` FROM usermeta WHERE 
      vartype="activate_code" AND user_id=?;', $uid);
    $this->request('user/activate', 'GET');
    $active = $db->value('SELECT active FROM user WHERE id=?;', $uid);
    $this->assertEquals(1, $active);
  }

  public function test_blocks()
  {
    global $db;
    Router::controller('blocks', 'core/controllers/BlocksController');
    Config::widgets([
      'html'=>'core/widgets/html',
      'image'=>'core/widgets/image']);
    Config::content('page', 'core/tables/page.php');
    new TableSchema('page');
    Session::user(self::$userId, 'Test', 'test@mail.com');
    $_POST = ['id'=>'page_1'];
    $response = $this->request('blocks/discard');
    $db->query('REPLACE INTO `page` SET id=1;');
    $db->query('UPDATE `page` SET blocks=\'\' WHERE id=1;');

    $_GET = ['id'=>'new', 'type'=>'html'];
    $response = $this->request('blocks/edit');
    $this->assertContains(' name="option[text]"', $response);
    $_GET = [];

    $_POST = ['id'=>'page_1_0', 'type'=>'html'];
    $response = $this->request('blocks/create', 'POST');
    $this->assertEquals('[{"_type":"html"}]', $response);
    $_POST = ['id'=>'page_1_1', 'type'=>'image'];
    $response = $this->request('blocks/create', 'POST');
    $image = '{"_type":"image","image":"$p=l1.jpg"}';
    $this->assertEquals('[{"_type":"html"},'.$image.']', $response);

    $_POST = ['widget_id'=>'page_1_0', 'option'=>['text'=>'<p  onclick="alert(0)">Something</p>']];
    $response = $this->request('blocks/update', 'POST');
    $this->assertEquals('[{"text":"<p>Something<\/p>","_type":"html"},'.$image.']', $response);

    $_POST = ['widget_id'=>'page_1_0', 'option'=>['text'=>'<script >alert(0)</script>Something</p>']];
    $response = $this->request('blocks/update', 'POST');
    $this->assertEquals('[{"text":"<p>Something<\/p>","_type":"html"},'.$image.']', $response);

    $_POST = ['widget_id'=>'page_1_0', 'option'=>['text'=>'<p><a href="javascript:alert(0)">Something</a></p>']];
    $response = $this->request('blocks/update', 'POST');
    $this->assertEquals('[{"text":"<p><a href=\"javascript&#8282;alert(0)\">Something<\/a><\/p>","_type":"html"},'.$image.']', $response);

    $_POST = ['widget_id'=>'page_1_0', 'option'=>['text'=>'<form><button href="javascript:alert(0)">xx</button></form>']];
    $response = $this->request('blocks/update', 'POST');
    $this->assertEquals('[{"text":"<form><button href=\"javascript&#8282;alert(0)\">xx<\/button><\/form>","_type":"html"},'.$image.']', $response);

    $_POST = ['widget_id'=>'page_1_0', 'option'=>['text'=>'<p>Something</p>']];
    $response = $this->request('blocks/update', 'POST');
    $this->assertEquals('[{"text":"<p>Something<\/p>","_type":"html"},'.$image.']', $response);

    $_POST = ['id'=>'page_1_1'];
    $response = $this->request('blocks/delete', 'POST');
    $this->assertEquals('[{"text":"<p>Something<\/p>","_type":"html"}]', $response);

    $_POST = ['id'=>'page_1'];
    $response = $this->request('blocks/save', 'POST');
    $blocks = $db->value('SELECT blocks FROM page WHERE id=1;');
    $this->assertEquals('[{"text":"<p>Something<\/p>","_type":"html"}]', $blocks);
  }

  public static function createUserTable()
  {
    global $db;
    $db->query('CREATE TABLE IF NOT EXISTS `user` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `username` varchar(80) DEFAULT NULL,
      `email` varchar(80) DEFAULT NULL,
      `pass` varchar(120) DEFAULT NULL,
      `active` tinyint(1) DEFAULT 0,
      `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
    
    $db->query('CREATE TABLE IF NOT EXISTS `usermeta` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `user_id` int(11) DEFAULT NULL,
      `vartype` varchar(80) DEFAULT NULL,
      `value` varchar(255) DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `user_id` (`user_id`),
      KEY `vartype` (`vartype`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
  }

  public function request($path, $method='GET')
  {
    $_SERVER['REQUEST_METHOD'] = $method;
    [$c, $a] = explode('/', $path);
    Router::$controller = $c;
    Router::$action = $a;
    ob_start();
    Router::run($path);
    $response = ob_get_contents();
    ob_end_clean();
    return $response;
  }
}
