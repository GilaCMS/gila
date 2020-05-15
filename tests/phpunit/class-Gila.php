<?php
chdir(__DIR__.'/../../');
include __DIR__.'/../../vendor/autoload.php';
include __DIR__.'/../../src/core/classes/Gila.php';
include __DIR__.'/../../src/core/classes/Router.php';
define("LOG_PATH", "log");
define("CONFIG_PHP", "config.php");

use PHPUnit\Framework\TestCase;

class ClassGila extends TestCase
{

	public function test_route()
	{
		Gila::route('test.txt', function(){ return 'test'; });
		$this->assertEquals(function(){ return 'test'; }, Gila::$route['test.txt']);
	}

	public function test_addLang()
	{
		Gila::config('language','es');
		Gila::addLang('core/lang/');
		$this->assertEquals('Inicio', __('Home'));
	}

	public function test_updateConfigFile()
	{
		$value = rand(1,100);
		include_once(__DIR__.'/../../config.default.php');
		Gila::setConfig('test_config_key', $value);
		Gila::updateConfigFile();
		include_once(__DIR__.'/../../config.php');
		$this->assertEquals($value, $GLOBALS['config']['test_config_key']);
	}

	public function test_mt()
	{
		$value = time();
		Gila::setMt('test_mt');
		$this->assertTrue(abs($value - Gila::mt('test_mt')) < 100);
	}

	public function test_url()
	{
		Gila::setConfig('default-controller', 'blog');
		Gila::setConfig('rewrite',0);
		$link = Gila::url('blog/post/1/post1');
		$this->assertEquals('?c=blog&action=post&var2=post1&var1=1', $link);
		Gila::setConfig('rewrite',1);
		$link = Gila::url('blog/post/1/post1');
		$this->assertEquals('post/1/post1', $link);
	}

	public function test_make_url()
	{
		Gila::setConfig('default-controller', 'blog');
		Gila::setConfig('rewrite',0);
		$link = Gila::make_url('blog', 'post', ['id'=>1,'slug'=>'post_1',]);
		$this->assertEquals('?c=blog&action=post&id=1&slug=post_1', $link);
		Gila::setConfig('rewrite',1);
		$link = Gila::make_url('blog', 'post', ['id'=>1,'slug'=>'post_1',]);
		$this->assertEquals('post/1/post_1', $link);
	}

}
