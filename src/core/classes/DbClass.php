<?php
/**
* A simple class for a mysqli connection
*/
namespace Gila;

class DbClass
{
  private $dbhost;
  private $user;
  private $pass;
  private $dsch;
  private $connected = false;
  public $link;
  public $result;
  public $profiling = '';
  public $replicas = [];
  private $replica;

  public function __construct($host = 'localhost', $user = 'root', $pass = '', $dsch = '')
  {
    if (is_array($host)) {
      $this->dbhost = $host['host'];
      $this->user = $host['user'];
      $this->pass = $host['pass'];
      $this->dsch = $host['name'];
    } else {
      $this->dbhost = $host;
      $this->user = $user;
      $this->pass = $pass;
      $this->dsch = $dsch;
    }
  }

  public function connect()
  {
    DB::connect();
  }

  public function close()
  {
    DB::close();
  }

  public function query($q, $args = null)
  {
    return DB::query($q, $args);
  }

  public function log($folder = false)
  {
    DB::log($folder);
  }

  public function execute($q, $args = null)
  {
    return DB::execute($q, $args);
  }

  public function res($v)
  {
    return DB::res($v);
  }

  public function multi_query($q)
  {
    return DB::multi_query($q);
  }

  public function get($q, $args = null)
  {
    return DB::get($q, $args);
  }

  public function getOne($q, $args = null)
  {
    return $this->get($q, $args)[0]??null;
  }

  public function gen($q, $args = null)
  {
    return DB::gen($q, $args);
  }

  public function getRows($q, $args = null)
  {
    return DB::getRows($q, $args);
  }

  public function getAssoc($q, $args = null)
  {
    return DB::getAssoc($q, $args);
  }

  public function getList($q, $args = null)
  {
    return DB::getList($q, $args);
  }

  public function getOptions($q, $args = null)
  {
    return DB::getOptions($q, $args);
  }

  public function error()
  {
    return DB::error();
  }

  public function value($q, $p = null)
  {
    return DB::value($q, $p);
  }

  public function read()
  {
    if (isset($this->replica)) {
      return $this->replica;
    }
    $count = count($this->replicas);
    if ($count>0) {
      $this->replica = new DbClass($this->replicas[rand(0, $count-1)]);
    } else {
      $this->replica = &$this;
    }
    return $this->replica;
  }

  public function create($table, $data)
  {
    return DB::create($table, $data);
  }
}
