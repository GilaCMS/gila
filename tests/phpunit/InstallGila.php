<?php
include __DIR__.'/../../vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class InstallGila extends TestCase
{
	public function testInstall()
	{
		$host = "localhost";
		$db = "g_db";
		$db_user = "g_user";
		$db_pass = "password";
		$email = "admin@mail.com";
		$pass = "password";
		$base = "http://localhost/html/";

		$default_opts = array(
			'http'=>array(
				'method'=>"POST",
				'header'=>"Accept-language: en-us;q=0.5,en;q=0.3\r\n".
					"Content-Type: application/x-www-form-urlencoded",
				'content'=>"db_host=$host&db_name=$db&db_user=$db_user&db_pass=$db_pass&adm_user=Admin&adm_email=$email&adm_pass=$pass&base_url=$base"
			)
		);

		stream_context_set_default($default_opts);
		$response = file_get_contents($base.'?install&step=1');

		$this->assertContains("Installation finished successfully!", $response);
    }
}
