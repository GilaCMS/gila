<?php

class session
{
    function __construct ()
    {
        global $db;
        //ini_set("session.save_handler", "files");
        //ini_set("session.save_path", __DIR__."/../../../sessions");
        //ini_set('session.gc_maxlifetime', 24*3600);
        session_set_cookie_params(24*3600);
        session_start();
        session::define(['user_id'=>0]);

        if (isset($_POST['username']) && isset($_POST['password'])) {
            $res = $db->query("SELECT id,pass FROM user WHERE email=?;",[$_POST['username']]);
            while ($r = mysqli_fetch_array($res)) if(password_verify($_POST['password'],$r[1])){
                $_SESSION[session::md5('user_id')] = $r[0];
                $chars = 'bcdfghjklmnprstvwxzaeiou123467890';
                $gsession='';
                for ($p = 0; $p < 50; $p++) $gsession .= $chars[mt_rand(0, 32)];
                $db->query("INSERT INTO usermeta(user_id,vartype,`value`) VALUES(?,'GSESSIONID',?);",[$r[0],$gsession]);
                setcookie('GSESSIONID', $gsession, time() + (86400 * 30), "/");
            }
        }
        if(session::user_id()==0) if(isset($_COOKIE['GSESSIONID'])) {
            $res = $db->query("SELECT user_id FROM usermeta WHERE value=? AND vartype='GSESSIONID';",[$_COOKIE['GSESSIONID']]);
            while ($r = mysqli_fetch_array($res)) {
                session::key('user_id',$r[0]);
            }
        }
    }

    static function define ($vars) {
        foreach ($vars as $k=>$v) {
            if(!isset($_SESSION[session::md5($k)])) $_SESSION[session::md5($k)]=$v;
        }
    }

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

    static function md5 ($key) {
        $dbname = $GLOBALS['config']['db']['name'];
        return md5($dbname.$key);
    }

    static function user_id ()
    {
        return $_SESSION[session::md5('user_id')];
    }

    static function destroy ()
    {
        session_destroy();
    }
}
