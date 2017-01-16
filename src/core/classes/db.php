<?php
/* db.php
Created: 04/10/2016 by Vasilis Zoumpourlis
Updated: 18/10/2016 by Vasilis Zoumpourlis

A simple class for a mysqli connection

example use
$db = new dbClass('localhost', 'root', '', '');
*/

class db {
	private $dbhost, $user, $pass, $dsch;

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
	}

	public function query($q, $args = null)
	{
		$link = mysqli_connect($this->dbhost, $this->user, $this->pass, $this->dsch);

		if ($args === null) {
            return $link->query($q);
        }
        else if (!is_array($args)) {
            $argsBkp = $args;
            $args = array($argsBkp);
        }

        $stmt = $link->prepare($q);
		$dt = "";
		foreach($args as $value) {
            $x=$link->real_escape_string($value);
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

		if(call_user_func_array([$stmt,'bind_param'], $refarg)) {
            $stmt->execute();
			return $stmt->get_result();
		}

		return false;
	}


	function multi_query($q)
	{
		$link = mysqli_connect($this->dbhost, $this->user, $this->pass, $this->dsch);
		$link->set_charset("utf8");
		$res = $link->multi_query($q);
	  mysqli_close($link);
	  return $res;
	}

	function get($q)
	{
		$arr = [];
		$link = mysqli_connect($this->dbhost, $this->user, $this->pass,  $this->dsch);
		$link->set_charset("utf8");
		//echo $q."<br>";
		$res = $link->query($q);
	  	mysqli_close($link);
		if($res) while($r=mysqli_fetch_array($res)) $arr[]=$r;
	  	return $arr;
	}

	function getRows($q)
	{
		$arr = [];
		$link = mysqli_connect($this->dbhost, $this->user, $this->pass,  $this->dsch);
		$link->set_charset("utf8");
		//echo $q."<br>";
		$res = $link->query($q);
	 	mysqli_close($link);
		while($r=mysqli_fetch_row($res)) $arr[]=$r;
	  	return $arr;
	}

	function getArray($q)
	{
		$arr=[];
		$garr=$this->getRows($q);
		foreach ($garr as $key => $value) $arr[]=$value[0];
	  	return $arr;
	}

	function getList($q)
	{
		$arr=[];
		$garr=$this->getRows($q);
		foreach ($garr as $key => $value) $arr[]=$value[0];
	  	return $arr;
	}

	function insert($q)
	{
		$link = mysqli_connect($this->dbhost, $this->user, $this->pass,  $this->dsch);
		$link->set_charset("utf8");
		//if( $link->query($q) ) $res = $link->insert_id; else $res = 0;
		$link->query($q);
		$res = $link->insert_id;
	  mysqli_close($link);
	  return $res;
	}

	function getCSV($q)
	{
		$arr = $this->get($q);
		return $arr;
	}

	function param($p) {
		return addslashes($_REQUEST[$p]);
	}
	function request($p) {
		//mysqli_real_escape_string($con, $_POST['age']);
		return addslashes($_REQUEST[$p]);
	}

	public function gen($q, $args = null)
	{
		$link = mysqli_connect($this->dbhost, $this->user, $this->pass, $this->dsch);

		$res = $link->query($q);
		mysqli_close($link);
		while ($r = mysqli_fetch_array($res)) {
			yield $r;
		}

	}

	function value($q)
	{
		$arr = [];
		$link = mysqli_connect($this->dbhost, $this->user, $this->pass,  $this->dsch);
		$link->set_charset("utf8");
		$res = $link->query($q);
	  	mysqli_close($link);
		if($res) {
			$r=mysqli_fetch_array($res);
			return $r[0];
		}
		return null;
	}

}
