<?php

class view
{
    private $part = array();

	function set($p,$v) {
		$this->part[$p]=$v;
	}

	function render($filePath) {
		foreach ($this->part as $key => $value) { $$key = $value; echo $key."=".$value." "; }
        $filePath = __DIR__.'/../../'.$filePath;

        if (file_exists($filePath)) {
            include $this->path_theme."header.php";
            include $filePath;
            include $this->path_theme."footer.php";
        }
        else {
            echo $filePath." file not found!";
        }
	}

    function displayFile($filepath) {
		$replace = array();
		foreach ($this->part as $key => $value) { $replace[$key] = '{'.$key.'}'; }
		//foreach ($this->part as $key => $value) { $$key = $value; }
		$temp = file_get_contents($filepath);
		echo str_replace($replace, $this->part, $temp);
	}
}
