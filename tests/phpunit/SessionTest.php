<?php

include __DIR__.'/includes.php';
include_once __DIR__.'/../../src/core/classes/Session.php';
use PHPUnit\Framework\TestCase;
use Gila\Session;

/**
 * 
 */
class SessionTest extends TestCase
{
	public function test_crud(){
		$gsessionid = 'JKSHGSDHGDFIDF123KJCHDC';
		Session::create(100, $gsessionid, '127.0.0.1', 'User Agent');
		$result = Session::find($gsessionid);
        $this->assertEquals($gsessionid, $result['gsessionid']);
        $this->assertEquals('127.0.0.1', $result['ip_address']);
		$this->assertEquals('User Agent', $result['user_agent']);
		$this->assertEquals(100, $result['user_id']);
		Session::remove($gsessionid);
		$result = Session::find($gsessionid);
		$this->assertNull($result);
	}
}
