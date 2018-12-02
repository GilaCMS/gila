<?php
chdir(__DIR__.'/../../');
include __DIR__.'/../../vendor/autoload.php';
include __DIR__.'/../../src/core/classes/gila.php';
use PHPUnit\Framework\TestCase;

class ClassGila extends TestCase
{
	public function test_cotrollers()
	{
		gila::controllers(['ctrl1'=>'path1','ctrl2'=>'path2']);
		$this->assertEquals('path2', gila::$controller['ctrl2']);
		$this->assertEquals('path1', gila::$controller['ctrl1']);
	}

	public function test_controller()
	{
		gila::controller('ctrlx','pathx','classx');
		$this->assertEquals('pathx', gila::$controller['ctrlx']);
		$this->assertEquals('classx', gila::$controllerClass['ctrlx']);
	}

	public function test_route()
	{
		gila::route('test.txt', function(){ return 'test'; });
		$this->assertEquals(function(){ return 'test'; }, gila::$route['test.txt']);
	}

	public function test_addLang()
	{
		gila::config('language','es');
		gila::addLang('core/lang/');
		$this->assertEquals('Inicio', __('Home'));
	}

	public function test_updateConfigFile()
	{
		$value = rand(1,100);
		include_once(__DIR__.'/../../config.php');
		gila::setConfig('test_config_key', $value);
		gila::updateConfigFile();
		include_once(__DIR__.'/../../config.php');
		$this->assertEquals($value, $GLOBALS['config']['test_config_key']);
	}

	public function test_mt()
	{
		$value = time();
		gila::setMt('test_mt');
		$this->assertTrue(abs($value - gila::mt('test_mt')) < 100);
	}

	public function test_url()
	{
		gila::setConfig('default-controller', 'blog');
		gila::setConfig('rewrite',0);
		$link = gila::url('blog/post/1/post1');
		$this->assertEquals('?c=blog&action=post&var2=post1&var1=1', $link);
		gila::setConfig('rewrite',1);
		$link = gila::url('blog/post/1/post1');
		$this->assertEquals('post/1/post1', $link);
	}

	public function test_make_url()
	{
		gila::setConfig('default-controller', 'blog');
		gila::setConfig('rewrite',0);
		$link = gila::make_url('blog', 'post', ['id'=>1,'slug'=>'post_1',]);
		$this->assertEquals('?c=blog&action=post&id=1&slug=post_1', $link);
		gila::setConfig('rewrite',1);
		$link = gila::make_url('blog', 'post', ['id'=>1,'slug'=>'post_1',]);
		$this->assertEquals('post/1/post_1', $link);
	}

}
