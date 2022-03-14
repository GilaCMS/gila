<?php

include __DIR__.'/includes.php';
include __DIR__.'/../../src/core/classes/Controller.php';
use PHPUnit\Framework\TestCase;
use Gila\Router;
use Gila\Request;

class RequestTest extends TestCase
{
  public function test_validate()
  {
    $_POST = [
      'one'=>1,
      'two'=>'two',
    ];

    $data = Request::validate([
      'one'=>'',
      'two'=>'required',
    ]);

    $this->assertEquals([
      'one'=>1,
      'two'=>'two',
    ], $data);
  }

}
