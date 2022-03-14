<?php

include __DIR__.'/includes.php';
include_once __DIR__.'/../../src/core/classes/User.php';
include __DIR__.'/../../src/core/classes/Controller.php';
use PHPUnit\Framework\TestCase;
use Gila\User;
use Gila\Router;
use Gila\Controller;

/**
 * 
 */
class UserTest extends TestCase
{
	public function test_create(){
		$result = User::create('test_create@email.com', '123');
		$this->assertTrue($result!==false);
		$result = User::create('test_create@email.com', '123');
		$this->assertFalse($result);
	}

	public function test_auth(){
		Router::controller('user', 'core/controllers/UserController');

		$_POST = ['email'=>'test_create@email.com', 'password'=>'123'];
		$response = json_decode($this->request('user/auth', 'POST'), true);
		$this->assertFalse($response['success']);

		$userId = User::create('test_auth@email.com', '123', 'Test', 1);
		$_POST = ['email'=>'test_auth@email.com', 'password'=>'123'];
		$response = json_decode($this->request('user/auth', 'POST'), true);
		$this->assertTrue($response['success']);
		$this->assertEquals($userId, $response['id']);
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
