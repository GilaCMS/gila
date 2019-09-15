<?php
include __DIR__.'/../../vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class LoginUser extends TestCase
{

  public function testLogin()
  {
    $email = "admin@mail.com";
    $pass = "password";
    $loginPage = "http://localhost/gilatest/login";

    $default_opts = [
      'http'=> [
        'method'=>'POST',
        'header'=>"Accept-language: en-us;q=0.5,en;q=0.3\r\n".
          "Content-Type: application/x-www-form-urlencoded",
        'content'=>http_build_query([
          'username'=>$email, 'password'=>$pass
        ])
      ]
    ];

    stream_context_set_default($default_opts);
    $response = file_get_contents($loginPage);

    $this->assertContains("<meta http-equiv='refresh' content='0;url=", $response);
  }
}
