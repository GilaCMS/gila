<?php
chdir(__DIR__.'/../../');
include __DIR__.'/../../vendor/autoload.php';
include __DIR__.'/../../src/core/classes/view.php';
include __DIR__.'/../../src/core/classes/gila.php';
include __DIR__.'/../../src/core/classes/router.php';
define("LOG_PATH", "log");
define("CONFIG_PHP", "config.php");

use PHPUnit\Framework\TestCase;

class ClassView extends TestCase
{
	public function test_getWidgetBody()
	{
		gila::widgets(['paragraph'=>'core/widgets/paragraph']);
		$html = view::getWidgetBody('paragraph', ['text'=>'Hello world']);
		$this->assertEquals('<p>Hello world</p>', $html);
	}

}
