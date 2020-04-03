<?php
chdir(__DIR__.'/../../');
include __DIR__.'/../../vendor/autoload.php';
include __DIR__.'/../../src/core/classes/gila.php';
include __DIR__.'/../../src/core/classes/router.php';
include __DIR__.'/../../src/core/classes/controller.php';
include __DIR__.'/../../src/core/controllers/fm.php';
define("LOG_PATH", "log");
define("CONFIG_PHP", "config.php");

use PHPUnit\Framework\TestCase;
$GLOBALS['user_privileges'] = ['admin'];
$c = new fm();

class ControllerFm extends TestCase
{

  public function test_allowedFiletype()
  {
    global $c;
    $list = ['csv'=>true, 'php'=>false, 'svg'=>false, 'twig'=>true];
    foreach ($list as $type=>$response) {
      $this->assertEquals($response, $c->allowedFiletype('path/to/file.of.'.$type));
    }
  }

  public function test_allowedPath()
  {
    global $c;
    $list = [
      'src/core/load.php'=>true, 'tmp/file.jpg'=>true, 'config.php'=>false,
      'assets/20/p.png'=>true, 'log/error.log'=>true, 'themes/blog/'=>true,
      '../'=>false, 'other_folder/'=>false, 'assets/..'=>false
    ];
    foreach ($list as $path=>$response) {
      $this->assertEquals($response, $c->allowedPath($path));
    }
  }

}
