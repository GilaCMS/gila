<?php
/* db.php
Created: 04/10/2016 by Vasilis Zoumpourlis
Updated: 18/10/2016 by Vasilis Zoumpourlis

A simple class for a mysqli connection

example use
$db = new dbClass('localhost', 'root', '', '');
*/

class dbClass {
	private $dbhost, $user, $pass, $dsch;

	function dbClass($dbhost = 'localhost', $user = 'root', $pass = '', $dsch = '')
	{
		$this->dbhost = $dbhost;
		$this->user = $user;
		$this->pass = $pass;
		$this->dsch = $dsch;
	}

	function query($q, $args = null)
	{
        if ($args === null) {
            return $link->query($q);
        }
        else if (!is_array($args)) {
            $argsBkp = $args;
            $args = array($argsBkp);
        }

        $stmt = $link->prepare($q);
        for($i=1;$i<count($p);$i++) {
            $x=$link->real_escape_string($p[$i]);
            $stmt->bind_param($p[0][$i], $x );
        }
        $stmt->execute();

		//$link = mysqli_connect($this->dbhost, $this->user, $this->pass, $this->dsch);
		//$link->set_charset("utf8");
		// SET GLOBAL time_zone = '+8:00';
		$res = $link->query($q);
	  mysqli_close($link);
	  return $res;
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

}
