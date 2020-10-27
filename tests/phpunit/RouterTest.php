<?php

include __DIR__.'/includes.php';
include __DIR__.'/../../src/core/classes/Controller.php';
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
  public function test_controller()
  {
    Gila\Router::controller('ctrlx', 'pathx');
    $this->assertEquals('pathx', Gila\Router::$controllers['ctrlx']);
  }

  public function test_action()
  {
    Gila\Router::controller('test', 'core/controllers/admin');
    Gila\Config::action('test', 'actionTest', function () {
      echo 'action test';
    });
    Gila\Router::setPath('test/actionTest/');
    $this->assertEquals('test', Gila\Router::getController());
    $this->assertEquals('actionTest', Gila\Router::getAction());
  }

  public function test_add()
  {
    Gila\Router::add('test.txt', function () {
      echo 'test.get';
    });
    Gila\Router::add('test.txt', function () {
      echo 'test.post';
    }, 'POST');
    Gila\Router::add('routex/(.*)', function ($x) {
      echo 'route#'.$x;
    }, 'GET');
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $this->assertEquals('test.get', $this->request('test.txt'));
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $this->assertEquals('test.post', $this->request('test.txt'));
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $this->assertEquals('route#8', $this->request('routex/8'));
  }

  public function test_param()
  {
    Gila\Router::$controllers = [];
    Gila\Router::$actions = [];
    $_GET['qx'] = 5;
    Gila\Router::setPath('test/action/p1/p2/p3/p4');
    Gila\Router::$controller = null;
    Gila\Router::$action = null;

    $this->assertEquals('test', Gila\Router::param('var1', 1));
    $this->assertEquals('p1', Gila\Router::param('var1', 3));
    $this->assertEquals('p1', Gila\Router::param('qx', 3));
    $this->assertEquals(5, Gila\Router::param('qx'));

    Gila\Router::controller('test', 'core/controllers/admin');
    $this->assertEquals('test', Gila\Router::getController());
    $this->assertEquals('action', Gila\Router::param('var1', 1));
    $this->assertEquals('p2', Gila\Router::param('var1', 3));

    Gila\Config::action('test', 'action', function () {
      echo 'action test';
    });
    $this->assertEquals('action', Gila\Router::getAction());
    $this->assertEquals('p1', Gila\Router::param('var1', 1));
    $this->assertEquals('p3', Gila\Router::param('var1', 3));
  }

  public function request($url)
  {
    ob_start();
    Gila\Router::run($url);
    $response = ob_get_contents();
    ob_end_clean();
    return $response;
  }
}
