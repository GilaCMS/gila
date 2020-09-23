<?php

include __DIR__.'/includes.php';
include __DIR__.'/../../src/core/classes/UserAgent.php';
use PHPUnit\Framework\TestCase;

class UserAgentTest extends TestCase
{
  public function test_isBot()
  {
    $this->assertFalse(Gila\UserAgent::isBot('Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:80.0) Gecko/20100101 Firefox/80.0'));
    $this->assertTrue(Gila\UserAgent::isBot('Mozilla/5.0 (compatible; SeznamBot/3.2; +http://napoveda.seznam.cz/en/seznambot-intro/)'));
    $this->assertTrue(Gila\UserAgent::isBot('SaaSHub'));
  }
}
