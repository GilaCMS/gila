<?php

use core\models\user;

class session
{
    function __construct ()
    {
        global $db;
        //ini_set("session.save_handler", "files");
        ini_set("session.save_path", __DIR__."/../../../log/sessions");
        //ini_set('session.gc_maxlifetime', 24*3600);
        session_set_cookie_params(24*3600);
        try {
            session_start();
        } catch (Exception $e) {

        }
        session::define(['user_id'=>0]);

        if (isset($_POST['username']) && isset($_POST['password'])) {
            $res = $db->query("SELECT id,pass,username,email FROM user WHERE email=?;",[$_POST['username']]);
            while ($r = mysqli_fetch_array($res)) if(password_verify($_POST['password'],$r[1])){
                session::key('user_id',$r[0]);
                session::key('user_name',$r[2]);
                session::key('user_email',$r[3]);
                $chars = 'bcdfghjklmnprstvwxzaeiou123467890';
                $gsession='';
                for ($p = 0; $p < 50; $p++) $gsession .= $chars[mt_rand(0, 32)];
                user::meta($r[0],'GSESSIONID',$gsession);
                setcookie('GSESSIONID', $gsession, time() + (86400 * 30), "/");
            }
        }
        if(session::user_id()==0) if(isset($_COOKIE['GSESSIONID'])) {
            foreach (user::getIdByMeta('GSESSIONID',$_COOKIE['GSESSIONID']) as $r) {
                $res = $db->query("SELECT id,username,email FROM user WHERE id=?;",[$r[0]]);
                while ($r = mysqli_fetch_array($res)) if(password_verify($_POST['password'],$r[1])){
                    session::key('user_id',$r[0]);
                    session::key('user_name',$r[1]);
                    session::key('user_email',$r[2]);
                }
            }
        }
    }

    /**
    * Define new session variables
    * @param $vars Associative Array
    */
    static function define ($vars) {
        foreach ($vars as $k=>$v) {
            if(!isset($_SESSION[session::md5($k)])) $_SESSION[session::md5($k)]=$v;
        }
    }

    /**
    * Returns or sets value for a session variable
    * @param $var (string) Variable name
    * @param $val (optional) Variable value, if is not set the function will return the current value
    * @param $t optional (int) Time in seconds to save the variable in the cookie (not saved if not set)
    * @return Variable value
    */
    static function key ($var,$val = null, $t = 0) {
        if ($val == null) {
            if(isset($_SESSION[session::md5($var)])) return $_SESSION[session::md5($var)]; else return null;
        }
        $_SESSION[session::md5($var)] = $val;

        if($t !== 0){
            if(is_object($val) || is_array($val)){
                $value = json_encode($val);
            }
            setcookie(session::md5($var), $val, (time() + $t), "/", $_SERVER["HTTP_HOST"]);
        }
    }

    /**
    * Unsets a session variable
    * @param $var (string) Variable name
    */
    static function unset ($var)
    {
        unset($_SESSION[session::md5($var)]);
    }

    private static function md5 ($key) {
        $dbname = $GLOBALS['config']['db']['name'];
        return md5($dbname.$key);
    }

    /**
    * Returns user id
    * @return int User's id. 0 if user is not logged in.
    */
    static function user_id ()
    {
        return $_SESSION[session::md5('user_id')];
    }

    /**
    * Destroys the session session
    */
    static function destroy ()
    {
        session_destroy();
    }
}
