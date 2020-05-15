<?php
chdir(__DIR__.'/../../');
include __DIR__.'/../../vendor/autoload.php';
include __DIR__.'/../../src/core/classes/View.php';
include __DIR__.'/../../src/core/classes/Gila.php';
include __DIR__.'/../../src/core/classes/Router.php';
include __DIR__.'/../../src/core/classes/Image.php';
include __DIR__.'/../../src/core/classes/FileManager.php';
define("LOG_PATH", "log");
define("SITE_PATH", "");
define("CONFIG_PHP", "config.php");

use PHPUnit\Framework\TestCase;

class ClassView extends TestCase
{
	public function test_getWidgetBody()
	{
		Gila::widgets(['paragraph'=>'core/widgets/paragraph']);
		$html = View::getWidgetBody('paragraph', ['text'=>'Hello world']);
		$this->assertEquals('<div style="padding:8px">\n<p>Hello world</p><div>', $html);
	}

	public function test_getThumbName()
	{
		$jpg1 = 'data/uploads/1.jpg';
		$jpg2 = 'data/uploads/2.jpg';
		file_put_contents($jpg1, '');
		file_put_contents($jpg2, '');
		file_put_contents('data/uploads/.thumbs.json', '{"1jpg200":"firstpath"}');
		$file = View::getThumbName('data/uploads/1.jpg', 200);
		$this->assertEquals("firstpath", $file);
		$file200 = View::getThumbName($jpg2, 200);
		$file300 = View::getThumbName($jpg2, 300);
		$this->assertFalse($file200 == $file300);
		$newfile200 = View::getThumbName($jpg2, 200);
		$newfile300 = View::getThumbName($jpg2, 300);
		$this->assertEquals($file200, $file200);
		$this->assertEquals($file300, $file300);
		FileManager::delete($jpg1);
		FileManager::delete($jpg2);
		FileManager::delete('data/uploads/.thumbs.json');
	}

}
