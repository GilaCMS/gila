<?php

include _DIR_.'/../../vendor/autoload.php';
include_once _DIR_.'/../../src/core/classes/User.php';
use PHPUnit\Framework\TestCase;
use Gila\User;

/**
 * 
 */
class userTest extends TestCase
{
	public static function test_create(){
		$result = User::create('test_create@email.com', '123');
		$this->assertTrue($result);
		$result = User::create('test_create@email.com', '123');
		$this->assertTrue($result);
	}
}


