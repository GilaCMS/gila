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
	public function test_create(){
		$result = User::sessions('1', 'Firefox', 'JKSHGSDHGDFIDF123KJCHDC');
        $this->assertTrue($result);
        $result = User::sessions('1', 'Firefox', 'JKSHGSDHGDFIDF123KJCHDC');
		$this->assertTrue($result);
	}
}


