<?php

use core\models\user;

class session
{
    private static $started = false;

    function __construct ()
    {
    }

    static function start ()
    {
        global $db;
        if(self::$started==true) return;
        //ini_set("session.save_handler", "files");
        //ini_set("session.save_path", __DIR__."/../../../log/sessions");
        //ini_set('session.gc_maxlifetime', 24*3600);
        session_set_cookie_params(24*3600);

        try {
            @session_start();
        } catch (Exception $e) {

        }
        self::$started = true;
        session::define(['user_id'=>0]);

        if (isset($_POST['username']) && isset($_POST['password'])) {
            $res = $db->query("SELECT id,pass,username,email FROM user WHERE email=?;",[$_POST['username']]);
            while ($r = mysqli_fetch_array($res)) if(password_verify($_POST['password'],$r[1])){
                session::log($r[0], $r[2], $r[3], 'Start');
                $chars = 'bcdfghjklmnprstvwxzaeiou123467890';
                $gsession = (string)$r[0];
                for ($p = strlen($gsession); $p < 50; $p++) $gsession .= $chars[mt_rand(0, 32)];
                user::meta($r[0],'GSESSIONID',$gsession);
                setcookie('GSESSIONID', $gsession, time() + (86400 * 30), "/");
            }
        } else {
            if(session::user_id()==0) if(isset($_COOKIE['GSESSIONID'])) {
                foreach (user::getIdsByMeta('GSESSIONID',$_COOKIE['GSESSIONID']) as $user_id) {
                    $res = $db->query("SELECT id,username,email FROM user WHERE id=?;",[$user_id]);
                    if ($r = mysqli_fetch_array($res)) {
                        session::user($r[0], $r[1], $r[2], 'By cookie');
                    }
                }
            }
        }
    }

    static function user ($id, $name, $email, $msg=null) {
        session::key('user_id',$id);
        session::key('user_name',$name);
        session::key('user_email',$email);
        if($msg!==null) {
            $session_log = new logger('log/sessions.log');
            $session_log->info($msg,['user_id'=>$id, 'email'=>$email]);
        }
    }

    /**
    * Define new session variables
    * @param $vars Associative Array
    */
    static function define ($vars) {
        self::start();
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
        self::start();
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
    static function unsetKey ($var)
    {
        self::start();
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
        self::start();
        return $_SESSION[session::md5('user_id')];
    }

    /**
    * Destroys the session session
    */
    static function destroy ()
    {
        if(self::user_id()>0) {
            $session_log = new logger('log/sessions.log');
            $session_log->info('End',['user_id'=>self::user_id(), 'email'=>self::key('user_email')]);
        }
        @session_destroy();
    }
}
