<?php

include(__DIR__.'/includes.php');
include(__DIR__.'/../../src/core/classes/Controller.php');
include(__DIR__.'/../../src/core/classes/Package.php');
include(__DIR__.'/../../src/core/models/User.php');
include(__DIR__.'/../../src/core/classes/gTable.php');
include(__DIR__.'/../../src/core/classes/TableSchema.php');
include(__DIR__.'/../../src/core/classes/gForm.php');
use PHPUnit\Framework\TestCase;
$GLOBALS['config']['db']['name'] = 'g_db';

class RequestsTest extends TestCase
{
  static protected $userId;

  static public function setUpBeforeClass()
  {
    global $db;
    self::createUserTable();
    $pass = Gila::hash("password");
    $db->query("INSERT INTO user SET email=?, pass=?, active=1;",
      ["test_login_auth@email.com", Gila::hash("password")]);
    $uid = $db->insert_id;
    $db->query("INSERT INTO usermeta SET `value`='ABC', user_id=?, `vartype`='token';", [$uid]);
    $db->query("INSERT INTO usermeta SET `value`=1, user_id=?, `vartype`='role';", [$uid]);
    self::$userId = $uid;
  }

  static public function tearDownAfterClass()
  {
    global $db;
    $db->query("DELETE FROM user WHERE email='test_login_auth@email.com';");
    $db->query("DELETE FROM usermeta WHERE user_id=?;", self::$userId);
  }

  public function test_login_auth()
  {
    $_POST['email'] = "test_login_auth@email.com";
    $_POST['password'] = "password";
    Gila::controller('login', 'core/controllers/login');
    $response = $this->request('login/auth', 'POST');
    $this->assertEquals('{"token":"ABC"}', $response);
  }

  public function test_blocks()
  {
    global $db;
    Gila::controller('blocks', 'blocks/controllers/blocks');
    Gila::widgets([
      'paragraph'=>'core/widgets/paragraph',
      'image'=>'core/widgets/image']);
    Package::update('blocks');
    Gila::table('post','core/tables/post.php');
    $gtable = new gTable('post');
    $gtable->update();
    Session::user(self::$userId, 'Test', 'test@mail.com');
    $_POST = ['id'=>'post_1'];
    $response = $this->request('blocks/discard');
    $db->query('INSERT INTO post SET id=1;');
    $db->query('UPDATE post SET blocks=\'\' WHERE id=1;');

    $_GET = ['id'=>'new', 'type'=>'paragraph'];
    $response = $this->request('blocks/edit');
    $this->assertContains('<vue-editor id="option[text]"', $response);
    $_GET = [];

    $_POST = ['id'=>'post_1_0', 'type'=>'paragraph'];
    $response = $this->request('blocks/create', 'POST');
    $this->assertEquals('[{"_type":"paragraph"}]', $response);
    $_POST = ['id'=>'post_1_1', 'type'=>'image'];
    $response = $this->request('blocks/create', 'POST');
    $image = '{"_type":"image","image":"assets\/core\/photo.png"}';
    $this->assertEquals('[{"_type":"paragraph"},'.$image.']', $response);

    $_POST = ['widget_id'=>'post_1_0', 'option'=>['text'=>'Something']];
    $response = $this->request('blocks/update', 'POST');
    $this->assertEquals('[{"text":"Something","_type":"paragraph"},'.$image.']', $response);

    $_POST = ['id'=>'post_1_1'];
    $response = $this->request('blocks/delete', 'POST');
    $this->assertEquals('[{"text":"Something","_type":"paragraph"}]', $response);

    $_POST = ['id'=>'post_1'];
    $response = $this->request('blocks/save', 'POST');
    $blocks = $db->value('SELECT blocks from post WHERE id=1;');
    $this->assertEquals('[{"text":"Something","_type":"paragraph"}]', $blocks);
  }

  static function createUserTable()
  {
    global $db;
    $db->query('CREATE TABLE IF NOT EXISTS `user` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `username` varchar(80) DEFAULT NULL,
      `email` varchar(80) DEFAULT NULL,
      `pass` varchar(120) DEFAULT NULL,
      `active` tinyint(1) DEFAULT 0,
      `reset_code` varchar(60) DEFAULT NULL,
      `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
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

  function request($url, $method='GET')
  {
    $_SERVER['REQUEST_METHOD'] = $method;
    [$c, $a] = explode('/', $url);
    Router::$controller = $c;
    Router::$action = $a;
    ob_start();
    Router::run($url);
    $response = ob_get_contents();
    ob_end_clean();
    return $response;
  }
}
