<?php

class view
{
    private $part = array();

	function set($p,$v) {
		$this->part[$p]=$v;
	}

	function display($temp) {
		foreach ($this->part as $key => $value) { $$key = $value; echo $key."=".$value." "; }
		echo $temp;
	}

    function displayFile($filepath) {
		$replace = array();
		foreach ($this->part as $key => $value) { $replace[$key] = '{'.$key.'}'; }
		//foreach ($this->part as $key => $value) { $$key = $value; }
		$temp = file_get_contents($filepath);
		echo str_replace($replace, $this->part, $temp);
	}
}
