<?php
/**
* A simple class for a mysqli connection
*/
class db {
	private $dbhost, $user, $pass, $dsch;
	private $connected,$link;
	public $insert_id,$result;

	function __construct($host = 'localhost', $user = 'root', $pass = '', $dsch = '')
	{
		if(is_array($host)) {
			$this->dbhost = $host['host'];
			$this->user = $host['user'];
			$this->pass = $host['pass'];
			$this->dsch = $host['name'];
		}
		else {
			$this->dbhost = $host;
			$this->user = $user;
			$this->pass = $pass;
			$this->dsch = $dsch;
		}
		$this->connected = false;
	}

	public function connect ()
	{
		if ($this->connected) return;
		$this->link = mysqli_connect($this->dbhost, $this->user, $this->pass, $this->dsch);
		$this->link->set_charset("utf8");
		$this->connected = true;
	}

	public function close ()
	{
		if (!$this->connected) return;
		mysqli_close($this->link);
		$this->connected = false;
	}

	public function query($q, $args = null)
	{
		if (!$this->connected) $this->connect();

		if ($args === null) {
            $res = $this->link->query($q);
			$this->insert_id = $this->link->insert_id;
			return $res;
        }

		$this->execute($q,$args);
		return $this->result;
	}

	public function execute($q, $args = null)
	{
		if (!is_array($args)) {
            $argsBkp = $args;
            $args = array($argsBkp);
        }

        $stmt = $this->link->prepare($q);
		$dt = "";
		foreach($args as $value) {
            $x=$this->link->real_escape_string($value);
			if(is_int($value)){
				$dt .= 'i';
			}else if(is_double($value)){
				$dt .= 'd';
			}else if(is_string($value)){
				$dt .= 's';
			}else{
				$dt .= 'b';
			}
        }
		array_unshift($args, $dt);
		$refarg = [];
		foreach ($args as $key => $value) $refarg[] =& $args[$key];

		if($stmt) if(call_user_func_array([$stmt,'bind_param'], $refarg)) {
            $stmt->execute();
			$this->insert_id = $this->link->insert_id;
			$this->result = $stmt->get_result();
		} else $this->result = false;
	}


	function multi_query($q)
	{
		if (!$this->connected) $this->connect();
		$res = $this->link->multi_query($q);
	  	$this->close();
	  	return $res;
	}

	public function insert($table,$params=[])
	{
		if (!$this->connected) $this->connect();
		$cols = implode(", ", array_keys($params));
		$vals = implode(", ", $params);
		$this->link->query("INSERT INTO $table($cols) VALUES($vals)");
	}

	public function get($q, $args = null)
	{
		$arr = [];
		$res = $this->query($q, $args);
		if($res) while($r=mysqli_fetch_array($res)) $arr[]=$r;
		$this->close();
	  	return $arr;
	}

	public function gen($q, $args = null)
	{
		$res = $this->query($q, $args);
		if($res) while($r=mysqli_fetch_array($res)) yield $r;
		$this->close();
	}

	function getRows($q, $args = null)
	{
		$arr = [];
		$res = $this->query($q, $args);
	 	$this->close();
		if($res) while($r=mysqli_fetch_row($res)) $arr[]=$r;
	  	return $arr;
	}

	function getAssoc($q, $args = null)
    {
        $arr = [];
        $res = $this->query($q, $args);
        $this->close();
        if($res) while($r=mysqli_fetch_assoc($res)) $arr[]=$r;
        return $arr;
    }

	function getArray($q)
	{
	  	return $this->getList;
	}

	function getList($q, $args = null)
	{
		$arr=[];
		$garr=$this->getRows($q, $args);
		foreach ($garr as $key => $value) $arr[]=$value[0];
	  	return $arr;
	}

	function error()
	{
		return mysqli_error($this->link);
	}


	function value($q,$p=null)
	{
		if($res = $this->query($q,$p)) {
			return mysqli_fetch_row($res)[0];
		}
		return null;
	}

}

/**
* @example $db
* @code
* global $db;
* $db = new dbClass('localhost', 'root', '', '');
* @endcode
*/
