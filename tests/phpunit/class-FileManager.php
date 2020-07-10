<?php

include(__DIR__.'/includes.php');
Gila\FileManager::$sitepath = realpath(__DIR__.'/../../');
use PHPUnit\Framework\TestCase;

class ClassFileManager extends TestCase
{

  public function test_allowedFiletype()
  {
    $list = ['csv'=>true, 'php'=>false, 'svg'=>false, 'twig'=>true];
    foreach ($list as $type=>$response) {
      $this->assertEquals($response,
        Gila\FileManager::allowedFileType('path/to/file.of.'.$type));
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
      $this->assertEquals($response, Gila\FileManager::allowedPath($path));
    }
  }

  public function test_copy_delete()
  {
    $p = 'assets/test-copy/';
    $p1 = 'assets/test-copy1/';
    Gila\Gila::dir($p);
    file_put_contents($p.'file1', '1');
    Gila\Gila::dir($p.'folder');
    file_put_contents($p.'folder/file2', '2');
    Gila\FileManager::copy($p, $p1);
    $this->assertEquals('1', file_get_contents($p1.'file1'));
    $this->assertEquals('2', file_get_contents($p1.'folder/file2'));
    Gila\FileManager::delete($p1.'file1');
    $this->assertFalse(file_exists($p1.'file1'));
    Gila\FileManager::delete($p1);
    $this->assertFalse(file_exists($p1.'folder/file2'));
    Gila\FileManager::delete($p);
  }

}
