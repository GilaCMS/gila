<?php
/**
* A simple class for a mysqli connection
*/
namespace Gila;

class DB
{
  private static $dbhost;
  private static $user;
  private static $pass;
  private static $dbname;
  private static $connected = false;
  private static $link;
  private static $replica;
  public static $insert_id;
  public static $result;
  public static $profiling = '';
  public static $replicas = [];

  static public function set($host = 'localhost', $user = 'root', $pass = '123', $dbname = 'Kal')
  {
    self::close();
    if (is_array($host)) {
      self::$dbhost = $host['host'];
      self::$user = $host['user'];
      self::$pass = $host['pass'];
      self::$dbname = $host['name'];
    } else {
      self::$dbhost = $host;
      self::$user = $user;
      self::$pass = $pass;
      self::$dbname = $dbname;
    }
  }

  static public function connect()
  {
    if (self::$connected) {
      return;
    }
    self::$link = mysqli_connect(self::$dbhost, self::$user, self::$pass, self::$dbname);
    if (self::$link===false) {
      Event::fire('Db::connect_error');
      exit;
    }
    self::$link->query('SET @@session.time_zone="-06:00";');
    //set_default_timezone('-6:00');
    Event::fire('Db::connect');
    if (self::$profiling) {
      self::$link->query('SET profiling=1;');
    }
    self::$connected = true;
  }

  static public function close()
  {
    if (!self::$connected) {
      return;
    }
    if (self::$profiling) {
      $res = self::$link->query('SHOW profiles;');
      if ($r=mysqli_fetch_row($res)) {
        $line = date("Y-m-d H:i:s").', '.$r[1]. ', "'.strtr($r[2], ['"'=>'\\"'])."\"\n";
        file_put_contents(self::$profiling.'/db.'.self::dbhost.'.log', $line, FILE_APPEND);
      }
    }
    mysqli_close(self::$link);
    self::$connected = false;
  }

  static public function query($q, $args = null)
  {
    if (!self::$connected) {
      self::connect();
    }

    if ($args === null) {
      $res = self::$link->query($q);
      self::$insert_id = self::$link->insert_id;
      return $res;
    } else {
      self::execute($q, $args);
      return self::$result;
    }
  }

  static public function log($folder = false)
  {
    self::$profiling = $folder;
  }

  static public function execute($q, $args = null)
  {
    if (!is_array($args)) {
      $argsBkp = $args;
      $args = [$argsBkp];
    }

    $stmt = self::$link->prepare($q);
    $dt = "";
    foreach ($args as $v) {
      $x = self::$link->real_escape_string($v);
      $dt .= is_int($v)? 'i': (is_string($v)? 's': (is_double($v)? 'd': 'b'));
    }
    array_unshift($args, $dt);
    $refarg = [];
    foreach ($args as $key => $value) {
      $refarg[] =& $args[$key];
    }

    if ($stmt) {
      if (call_user_func_array([$stmt,'bind_param'], $refarg)) {
        $stmt->execute();
        self::$insert_id = self::$link->insert_id;
        self::$result = $stmt->get_result();
        if (self::$link->error) {
          error_log('DB: '.self::$link->error, 3, 'log/error.log');
        }
      } else {
        self::$result = false;
      }
    }
  }

  static public function res($v)
  {
    if (!self::$connected) {
      self::connect();
    }
    return mysqli_real_escape_string(self::$link, $v);
  }

  static public function multi_query($q)
  {
    if (!self::$connected) {
      self::connect();
    }
    $res = self::$link->multi_query($q);
    self::close();
    return $res;
  }

  static public function get($q, $args = null)
  {
    $arr = [];
    $res = self::query($q, $args);
    if ($res) {
      while ($r=mysqli_fetch_array($res)) {
        $arr[]=$r;
      }
    }
    self::close();
    return $arr;
  }

  static public function getOne($q, $args = null)
  {
    return self::get($q, $args)[0]??null;
  }

  static public function gen($q, $args = null)
  {
    $res = self::query($q, $args);
    if ($res) {
      while ($r=mysqli_fetch_array($res)) {
        yield $r;
      }
    }
    self::close();
  }

  static public function getRows($q, $args = null)
  {
    $arr = [];
    $res = self::query($q, $args);
    self::close();
    if ($res) {
      while ($r=mysqli_fetch_row($res)) {
        $arr[]=$r;
      }
    }
    return $arr;
  }

  static public function getAssoc($q, $args = null)
  {
    $arr = [];
    $res = self::query($q, $args);
    self::close();
    if ($res) {
      if ($res === true) {
        return [];
      }
      while ($r=mysqli_fetch_assoc($res)) {
        $arr[]=$r;
      }
    }
    return $arr;
  }

  static public function getList($q, $args = null)
  {
    $arr=[];
    $garr=self::getRows($q, $args);
    foreach ($garr as $key => $value) {
      $arr[]=$value[0];
    }
    return $arr;
  }

  static public function getOptions($q, $args = null)
  {
    $arr=[];
    $garr=self::getRows($q, $args);
    foreach ($garr as $key => $value) {
      $arr[$value[0]]=$value[1];
    }
    return $arr;
  }

  static public function error()
  {
    return self::$link->error??null;
  }

  static public function value($q, $p = null)
  {
    if ($res = self::getOne($q, $p)) {
      return $res[0];
    }
    return null;
  }

  static public function read()
  {
    if (isset(self::$replica)) {
      return self::$replica;
    }
    $count = count(self::$replicas);
    if ($count>0) {
      self::$replica = rand(0, $count-1);
    }
  }

  static public function create($table, $data)
  {
    $fields = [];
    $values = [];
    foreach ($data as $f=>$v) {
      $fields[] = '`'.$f.'`';
      $values[] = '"'.self::res($v).'"';
    }
    $fields = implode(',', $fields);
    $values = implode(',', $values);

    self::query("INSERT INTO $table($fields) VALUES($values);");
    return self::$insert_id ?? null;
  }
}
