<?php

include __DIR__.'/includes.php';
use PHPUnit\Framework\TestCase;
use Gila\Config;

class ConfigTest extends TestCase
{
  public function test_addLang()
  {
    Config::set('language', 'es');
    Config::addLang('core/lang/');
    $this->assertEquals('Inicio', __('Home'));
  }

  public function test_getArray()
  {
    $GLOBALS['config']['test_config_key'] = '["1","2"]';
    $this->assertEquals([0=>'1',1=>'2'], Config::getArray('test_config_key'));
  }

  public function test_mt()
  {
    $value = time();
    Config::setMt('test_mt');
    $this->assertTrue(abs($value - Config::mt('test_mt')) < 100);
  }

  public function test_url()
  {
    Config::set('default-controller', 'blog');
    $link = Config::url('blog/post/1/post1');
    $this->assertEquals('post/1/post1', $link);
    $link = Config::url('blog/post', ['id'=>1,'slug'=>'post_1']);
    $this->assertEquals('post?id=1&amp;slug=post_1', $link);
    $link = Config::url('blog/post?null');
    $this->assertEquals('post?null', $link);
  }

}
