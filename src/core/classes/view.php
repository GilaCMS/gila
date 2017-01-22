<?php

class view
{
    private static $part = array();

	static function set($p,$v) {
		self::$part[$p]=$v;
	}

	static function render($file, $package = null) {
		foreach (self::$part as $key => $value) { $$key = $value; }

        if ($package != null) {
            $filePath = 'src/'.$package.'/views/'.$file;
            if (file_exists($filePath)) {
                include $filePath;
                return;
            }
        }

        $filePath = 'themes/'.gila::config('theme').'/'.$file; // '/views/'.

        if (file_exists($filePath)) {
            include $filePath;
        }
        else {
            $filePath = 'src/core/views/'.$file;
            if (file_exists($filePath)) {
                include $filePath;
            } else trigger_error("View file not found ($filePath)");
        }
	}

    static function widget ($widget) {

        $filePath = 'themes/'.gila::config('theme').'/widgets/'.$widget.'.php'; // '/views/'.

        if (file_exists($filePath)) {
            include $filePath;
        }
        else {
            $filePath = 'src/core/widgets/'.$widget.'.php';
            if (file_exists($filePath)) {
                include $filePath;
            }
            else {
                echo $filePath." file not found!";
            }
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
