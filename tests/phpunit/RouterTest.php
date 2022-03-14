<?php

include __DIR__.'/includes.php';
include __DIR__.'/../../src/core/classes/Controller.php';
use PHPUnit\Framework\TestCase;
use Gila\Router;

class RouterTest extends TestCase
{
  public function test_controller()
  {
    Router::controller('ctrlx', 'pathx');
    $this->assertEquals('pathx', Router::$controllers['ctrlx']);
  }

  public function test_action()
  {
    Router::controller('test', 'core/controllers/admin');
    Router::action('test', 'actionTest', function () {
      echo 'action test';
    });
    Router::setPath('test/actionTest/');
    $this->assertEquals('test', Router::getController());
    $this->assertEquals('actionTest', Router::getAction());
  }

  public function test_add()
  {
    Router::add('test.txt', function () {
      echo 'test.get';
    });
    Router::add('test.txt', function () {
      echo 'test.post';
    }, 'POST');
    Router::add('routex/(.*)', function ($x) {
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
    Router::$controllers = [];
    Router::$actions = [];
    $_GET['qx'] = 5;
    Router::setPath('test/action/p1/p2/p3/p4');
    Router::$controller = null;
    Router::$action = null;

    $this->assertEquals('test', Router::param('var1', 1));
    $this->assertEquals('p1', Router::param('var1', 3));
    $this->assertEquals('p1', Router::param('qx', 3));
    $this->assertEquals(5, Router::param('qx'));

    Router::controller('test', 'core/controllers/admin');
    $this->assertEquals('test', Router::getController());
    $this->assertEquals('action', Router::param('var1', 1));
    $this->assertEquals('p2', Router::param('var1', 3));

    Router::action('test', 'action', function () {
      echo 'action test';
    });
    $this->assertEquals('action', Router::getAction());
    $this->assertEquals('p1', Router::param('var1', 1));
    $this->assertEquals('p3', Router::param('var1', 3));
  }

  public function request($url)
  {
    ob_start();
    Router::run($url);
    $response = ob_get_contents();
    ob_end_clean();
    return $response;
  }
}
