<?php
chdir(__DIR__.'/../../');
include __DIR__.'/../../vendor/autoload.php';
include __DIR__.'/../../src/core/classes/Db.php';
include __DIR__.'/../../src/core/classes/Gila.php';
include __DIR__.'/../../src/core/classes/Router.php';
include __DIR__.'/../../src/core/classes/controller.php';
include __DIR__.'/../../src/core/models/user.php';
define('SITE_PATH', '');
define('CONFIG_PHP', 'config.php');
//$db = new Db("127.0.0.1", "g_user", "password", "g_db");
$db = new Db("127.0.0.1", "guser", "12345", "gila150");

use PHPUnit\Framework\TestCase;

class RequestsTest extends TestCase
{

  public function test_login_auth()
  {
    global $db;
    $this->createUserTable();
    $pass = Gila::hash("password");
    $db->query("INSERT INTO user SET email=?, pass=?, active=1;",
      ["test_login_auth@email.com", Gila::hash("password")]);
    $db->query("INSERT INTO usermeta SET `value`='ABC', user_id=?, `vartype`='token';",
      [$db->insert_id]);
    $_POST['email'] = "test_login_auth@email.com";
    $_POST['password'] = "password";
    Gila::controller('login', 'core/controllers/login');
    $response = $this->request('login/auth');
    $this->assertEquals('{"token":"ABC"}', $response);
    $db->query("DELETE FROM user WHERE email='test_login_auth@email.com';");
  }

  function createUserTable()
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

  function request($url)
  {
    ob_start();
    Router::run($url);
    $response = ob_get_contents();
    ob_end_clean();
    return $response;
  }
}
