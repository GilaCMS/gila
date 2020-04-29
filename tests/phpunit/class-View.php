<?php
chdir(__DIR__.'/../../');
include __DIR__.'/../../vendor/autoload.php';
include __DIR__.'/../../src/core/classes/View.php';
include __DIR__.'/../../src/core/classes/Gila.php';
include __DIR__.'/../../src/core/classes/Router.php';
define("LOG_PATH", "log");
define("CONFIG_PHP", "config.php");

use PHPUnit\Framework\TestCase;

class ClassView extends TestCase
{
	public function test_getWidgetBody()
	{
		Gila::widgets(['paragraph'=>'core/widgets/paragraph']);
		$html = View::getWidgetBody('paragraph', ['text'=>'Hello world']);
		$this->assertEquals('<p>Hello world</p>', $html);
	}

}
