<?php

include __DIR__.'/includes.php';
use PHPUnit\Framework\TestCase;
use Gila\Config;

class ClassGila extends TestCase
{

	public function test_addLang()
	{
		Config::config('language','es');
		Config::addLang('core/lang/');
		$this->assertEquals('Inicio', __('Home'));
	}

	public function test_updateConfigFile()
	{
		$value = rand(1,100);
		include_once __DIR__.'/../../config.default.php';
		Config::setConfig('test_config_key', $value);
		Config::updateConfigFile();
		include_once __DIR__.'/../../config.php';
		$this->assertEquals($value, $GLOBALS['config']['test_config_key']);
	}

	public function test_mt()
	{
		$value = time();
		Config::setMt('test_mt');
		$this->assertTrue(abs($value - Config::mt('test_mt')) < 100);
	}

	public function test_url()
	{
		Config::setConfig('default-controller', 'blog');
		Config::setConfig('rewrite',0);
		$link = Config::url('blog/post/1/post1');
		$this->assertEquals('?c=blog&action=post&var2=post1&var1=1', $link);
		Config::setConfig('rewrite',1);
		$link = Config::url('blog/post/1/post1');
		$this->assertEquals('post/1/post1', $link);
	}

	public function test_make_url()
	{
		Config::setConfig('default-controller', 'blog');
		Config::setConfig('rewrite',0);
		$link = Config::make_url('blog', 'post', ['id'=>1,'slug'=>'post_1',]);
		$this->assertEquals('?c=blog&action=post&id=1&slug=post_1', $link);
		Config::setConfig('rewrite',1);
		$link = Config::make_url('blog', 'post', ['id'=>1,'slug'=>'post_1',]);
		$this->assertEquals('post/1/post_1', $link);
	}

}
