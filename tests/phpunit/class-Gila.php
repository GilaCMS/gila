<?php

include(__DIR__.'/includes.php');
use PHPUnit\Framework\TestCase;

class ClassGila extends TestCase
{

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
		Gila\Gila::setConfig('test_config_key', $value);
		Gila\Gila::updateConfigFile();
		include_once(__DIR__.'/../../config.php');
		$this->assertEquals($value, $GLOBALS['config']['test_config_key']);
	}

	public function test_mt()
	{
		$value = time();
		Gila\Gila::setMt('test_mt');
		$this->assertTrue(abs($value - Gila\Gila::mt('test_mt')) < 100);
	}

	public function test_url()
	{
		Gila\Gila::setConfig('default-controller', 'blog');
		Gila\Gila::setConfig('rewrite',0);
		$link = Gila\Gila::url('blog/post/1/post1');
		$this->assertEquals('?c=blog&action=post&var2=post1&var1=1', $link);
		Gila\Gila::setConfig('rewrite',1);
		$link = Gila\Gila::url('blog/post/1/post1');
		$this->assertEquals('post/1/post1', $link);
	}

	public function test_make_url()
	{
		Gila\Gila::setConfig('default-controller', 'blog');
		Gila\Gila::setConfig('rewrite',0);
		$link = Gila\Gila::make_url('blog', 'post', ['id'=>1,'slug'=>'post_1',]);
		$this->assertEquals('?c=blog&action=post&id=1&slug=post_1', $link);
		Gila\Gila::setConfig('rewrite',1);
		$link = Gila\Gila::make_url('blog', 'post', ['id'=>1,'slug'=>'post_1',]);
		$this->assertEquals('post/1/post_1', $link);
	}

}
