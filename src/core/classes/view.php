<?php

class view
{
    private static $part = array();

	static function set($p,$v) {
		self::$part[$p]=$v;
	}

	static function render($file) {
		foreach (self::$part as $key => $value) { $$key = $value; }
        //$filePath = __DIR__.'/../../'.$filePath;
        $filePath = 'themes/'.$GLOBALS['config']['theme'].'/'.$file; // '/views/'.

        if (file_exists($filePath)) {
            include $filePath;
        }
        else {
            $filePath = __DIR__.$file;
            if (file_exists($filePath)) {
                include $filePath;
            }else  echo $filePath." file not found!";
        }
	}

    static function widget ($widget) {
        $filePath = 'themes/'.$GLOBALS['config']['theme'].'/widgets/'.$widget.'.php'; // '/views/'.

        if (file_exists($filePath)) {
            include $filePath;
        }
        else {
            $filePath = __DIR__.'/widgets/'.$widget.'.php';
            if (file_exists($filePath)) {
                include $filePath;
            }// else  echo $filePath." file not found!";
        }
    }
/*
    function displayFile($filepath) {
		$replace = array();
		foreach ($this->part as $key => $value) { $replace[$key] = '{'.$key.'}'; }
		//foreach ($this->part as $key => $value) { $$key = $value; }
		$temp = file_get_contents($filepath);
		echo str_replace($replace, $this->part, $temp);
	}*/
}
