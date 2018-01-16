<?php

class gilax {
    function mailAdmin($message)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $baseurl = gila::config('base');

        $email = gila::config('admin_email');
        $subject = "Message from ".$baseurl;
        $message = "";
        $headers = "From: GilaCMS <noreply@{$_SERVER['HTTP_HOST']}>";

        foreach($_POST as $key->$post) {
            $message = "$key:\n$post\n\n";
        }

        echo "<textarea>$message</textarea>";

        mail($email,$subject,$message,$headers);

        view::includeFile('login-change-emailed.php');
    }
}
