<?php

include __DIR__.'/includes.php';
include_once __DIR__.'/../../src/core/classes/User.php';
use PHPUnit\Framework\TestCase;
use Gila\User;

/**
 * 
 */
class UserTest extends TestCase
{
	public static function test_create(){
		$result = User::create('test_create@email.com', '123');
		$this->assertTrue($result);
		$result = User::create('test_create@email.com', '123');
		$this->assertTrue($result);
	}
}


