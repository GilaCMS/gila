<?php

class session
{
    function __construct ()
    {
        //global $db;
        ini_set("session.save_handler", "files");
        ini_set("session.save_path", __DIR__."/../../../sessions");
        session_start();
        session::define(['user_id'=>0]);

        if (isset($_POST['username']) && isset($_POST['password'])) {
        //    $res = $db->query("SELECT id FROM user WHERE email='?' AND password='?'",[$_POST['username'],$_POST['password']]);
        //    while ($r = mysqli_fetch_assoc($res)) {
        //        $_SESSION['user_id'] = $r[0];
        //    }
        }
    }

    static function define ($vars) {
        foreach ($vars as $k=>$v) {
            if(!isset($_SESSION[$k])) $_SESSION[$k]=$v;
        }
    }

    static function set ($var,$val) {
        $_SESSION[$val] = $val;
    }

    static function user_id ()
    {
        return $_SESSION['user_id'];
    }

    static function destroy ()
    {
        session_destroy();
    }
}
