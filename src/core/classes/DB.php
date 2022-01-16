<?php
/**
* A simple class for a mysqli connection
*/
namespace Gila;

class DB
{
  private $dbhost;
  private $user;
  private $pass;
  private $dsch;
  private $connected = false;
  private $link;
  public $insert_id;
  public $result;
  public $profiling = '';
  public $replicas = [];
  private $replica;

  static public function add($host, $user, $pass, $dsch)
  {
    self::$dbhost = $host;
    self::$user = $user;
    self::$pass = $pass;
    self::$dsch = $dsch;
  }

  static public function connect()
  {
    if (self::$connected) {
      return;
    }
    self::$link = mysqli_connect(self::$dbhost, self::$user, self::$pass, self::$dsch);
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
      self::$connect();
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
      } else {
        self::$result = false;
      }
    }
  }

  static public function res($v)
  {
    if (!self::$connected) {
      self::$connect();
    }
    return mysqli_real_escape_string(self::$link, $v);
  }

  static public function multi_query($q)
  {
    if (!self::$connected) {
      self::$connect();
    }
    $res = self::$link->multi_query($q);
    self::close();
    return $res;
  }

  static public function insert($table, $params = [])
  {
    if (!self::$connected) {
      self::$connect();
    }
    $cols = implode(", ", array_keys($params));
    $vals = implode(", ", $params);
    self::$link->query("INSERT INTO $table($cols) VALUES($vals)");
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
      self::$replica = new Db(self::$replicas[rand(0, $count-1)]);
    } else {
      self::$replica = &$this;
    }
    return self::$replica;
  }
}
