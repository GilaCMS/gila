<?php

include __DIR__.'/includes.php';
use PHPUnit\Framework\TestCase;
use Gila\Config;
use Gila\FileManager;

FileManager::$sitepath = realpath(__DIR__.'/../../');

class ClassFileManager extends TestCase
{
  public function test_allowedFiletype()
  {
    $list = ['csv'=>true, 'php'=>false, 'svg'=>false, 'twig'=>true];
    foreach ($list as $type=>$response) {
      $this->assertEquals(
        $response,
        FileManager::allowedFileType('path/to/file.of.'.$type)
      );
    }
  }

  public function test_allowedPath()
  {
    $list = [
      'src/core/load.php'=>true, 'config.php'=>false, 'themes/gila-blog/'=>true,
      'assets'=>true, 'log/'=>true, 'assets/..'=>false,
      '../'=>false, 'other_folder/'=>false
    ];
    foreach ($list as $path=>$response) {
      $this->assertEquals($response, FileManager::allowedPath($path));
    }
  }

  public function test_copy_delete()
  {
    $p = 'assets/test-copy/';
    $p1 = 'assets/test-copy1/';
    Config::dir($p);
    file_put_contents($p.'file1', '1');
    Config::dir($p.'folder');
    file_put_contents($p.'folder/file2', '2');
    FileManager::copy($p, $p1);
    $this->assertEquals('1', file_get_contents($p1.'file1'));
    $this->assertEquals('2', file_get_contents($p1.'folder/file2'));
    FileManager::delete($p1.'file1');
    $this->assertFalse(file_exists($p1.'file1'));
    FileManager::delete($p1);
    $this->assertFalse(file_exists($p1.'folder/file2'));
    FileManager::delete($p);
  }
}
