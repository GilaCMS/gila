<?php

class Filemanager
{
  static function copy($source, $target)
  {
    if(is_dir($source)) {
      $target = Gila::dir($target);
      $files = scandir($source);
      foreach ($files as $file) if ($file!='.' && $file!='..') {
        self::copy($source.'/'.$file, $target.'/'.$file);
      }
    } else {
      copy($source, $target);
    }
  }
}
