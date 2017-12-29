<?php


use core\models\user as user;

class login extends controller
{

    function __construct ()
    {

    }

	function indexAction()
    {
        if(session::key('user_id')>0) {
           echo "<meta http-equiv='refresh' content='0;url=".gila::config('base')."' />";
           exit;
        }
		view::includeFile('login.php');
    }

    function callbackAction()
    {
        event::fire('login.callback');
    }

	function registerAction()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && event::get('recaptcha',true)) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $name = $_POST['name'];
            if(user::getByEmail($email)) {
                // throw error
            }
            else {
                // register the user
                if(user::create($email,$password,$name)) {
                    // success
                    view::includeFile('login-register-success.php');
                    return;
                } else {
                    // throw error
                }
            }
        }
        view::includeFile('register.php');
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
                view::includeFile('login-change-success.php');
				exit;
			} else {
				view::includeFile('login-change-new.php');
				exit;
			}
		}

		if(!isset($_POST['email'])) {
			view::includeFile('login-change-password.php');
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
        $message .= $baseurl."login/password_reset?rp=$reset_code\n\n";
		$message .= "If you didn't ask to change the password please ignore this email";

		$headers = "From: GilaCMS <noreply@{$_SERVER['HTTP_HOST']}>";
		user::meta($r['id'],'reset_code',$reset_code);
		mail($email,$subject,$message,$headers);

		view::includeFile('login-change-emailed.php');
	}
}
