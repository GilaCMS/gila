<?php

include __DIR__.'/includes.php';
include __DIR__.'/../../src/core/classes/Image.php';
use PHPUnit\Framework\TestCase;
use Gila\FileManager;
use Gila\View;
use Gila\Config;

class ViewTest extends TestCase
{
  public function test_getWidgetBody()
  {
    Config::widgets(['paragraph'=>'core/widgets/paragraph']);
    $html = View::getWidgetBody('paragraph', ['text'=>'Hello world']);
    $this->assertEquals('<section>Hello world</section>', $html);
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
