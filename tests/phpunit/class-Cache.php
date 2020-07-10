<?php

include(__DIR__.'/includes.php');
include_once(__DIR__.'/../../src/core/classes/Cache.php');
use PHPUnit\Framework\TestCase;

class ClassCache extends TestCase
{

	public function test_remember()
	{
		FileManager::delete(LOG_PATH.'/cacheItem/itemTest1|');
		FileManager::delete(LOG_PATH.'/cacheItem/itemTest2|');
		$data = Gila\Cache::get('itemTest1');
		$this->assertEquals(null, $data);
		Gila\Cache::set('itemTest1', 'data1');
		$data = Gila\Cache::get('itemTest1');
		$this->assertEquals('data1', $data);

		$data = Gila\Cache::remember('itemTest2', 3600, function(){
			return 'data2';
		}, [1]);
		$this->assertEquals('data2', $data);
		$data = Gila\Cache::remember('itemTest2', 3600, function(){
			return 'data2-updated';
		}, [1]);
		$this->assertEquals('data2', $data);
		$data = Gila\Cache::remember('itemTest2', 3600, function(){
			return 'data2-updated';
		}, [2]);
		$this->assertEquals('data2-updated', $data);
	}
}
