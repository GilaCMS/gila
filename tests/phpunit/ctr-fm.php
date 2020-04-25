<?php
chdir(__DIR__.'/../../');
include __DIR__.'/../../vendor/autoload.php';
include __DIR__.'/../../src/core/classes/Gila.php';
include __DIR__.'/../../src/core/classes/Router.php';
include __DIR__.'/../../src/core/classes/controller.php';
include __DIR__.'/../../src/core/controllers/fm.php';
define('SITE_PATH', '');
define('LOG_PATH', 'log');
define('CONFIG_PHP', 'config.php');
define('FS_ACCESS', true);

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
      'src/core/load.php'=>true, 'config.php'=>false, 'themes/gila-blog/'=>true,
      'assets'=>true, 'log/'=>true, 'assets/..'=>false,
      '../'=>false, 'other_folder/'=>false
    ];
    foreach ($list as $path=>$response) {
      echo "$path ";
      $this->assertEquals($response, $c->allowedPath($path));
    }
  }

}
