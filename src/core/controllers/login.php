<?php


use core\models\user as user;

class login extends controller
{

    function __construct ()
    {

    }

	function indexAction()
    {
		include 'src/core/views/login.phtml';
    }

	function registerAction()
    {
		include 'src/core/views/register.phtml';
    }

	function password_resetAction()
	{
		if(isset($_GET['rp'])) {
            $r = user::getByResetCode($_GET['rp']);
			if (!$r) {
            echo 'This reset password code has been expired or used.';
  				  exit;
			}
			else if(isset($_POST['pass'])) {
				$idUser=$r[0];
        user::updatePassword($idUser,$_POST['pass']);
				exit;
			} else {
				include 'src/core/views/new_password.phtml';
				exit;
			}
		}

		if(!isset($_POST['email'])) {
			include 'src/core/views/reset_password.php';
			return;
		}

		$email = $_POST['email'];
		$out = [];

		$r = user::getByEmail($email);

		if ($r == false) {
  			echo "No user found with this email.";
  			$out['success'] = false;
  			$out['msg'] = "No user found with this email.";
  			exit;
		}
    $baseurl = gila::config('base');
		$subject = "Change Password Code for ".$r['username'];
		$reset_code = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'),0,50);
        $message = "Hello {$r['username']}\n\n";
        $message .= "This is the link to change your password in $baseurl\n\n";
		$message .= $baseurl."login/password_reset.php?rp=$reset_code\n\n";
		$message .= "If you didn't ask to change the password please ignore this email";

		$headers = "From: GilaCMS <noreply@{$_SERVER['HTTP_HOST']}>";
		user::meta($r['id'],'reset_code',$reset_code);
		mail($email,$subject,$message,$headers);

		$out['success'] = true;
		$out['msg'] = "An email has been send to you in order to reset you password.";
		echo "OK";
	}
}
