<?php

class FileManager
{
  public static $sitepath = __DIR__;

  public static function copy($source, $target)
  {
    if (is_dir($source)) {
      $target = Gila::dir($target);
      $files = scandir($source);
      foreach ($files as $file) {
        if ($file!='.' && $file!='..') {
          self::copy($source.'/'.$file, $target.'/'.$file);
        }
      }
    } else {
      copy($source, $target);
    }
  }

  public static function delete($target)
  {
    if (is_dir($target)) {
      $files = scandir($target);
      foreach ($files as $file) {
        if ($file!='.' && $file!='..') {
          self::delete($target.'/'.$file);
        }
      }
      @rmdir($target);
    } else {
      @unlink($target);
    }
  }

  public static function allowedFileType($path)
  {
    $filetypes = [
      'txt','json','css','pdf','twig','csv','tsv','log',
      'png','jpg','jpeg','gif','webp','ico',
      'avi','webm','mp4','mkv','ogg'
    ];
    if (is_dir($path)) {
      return true;
    }
    if (Gila::config('allow_filetypes') && Session::hasPrivilege('admin')) {
      $filetypes = merge_array($filetypes, Gila::config('allow_filetypes'));
    }
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if (in_array($ext, $filetypes)) {
      return true;
    }
    return false;
  }

  public static function allowedPath($path)
  {
    $allowedPaths = ['tmp','log','data/public'];
    $allowedPaths[] = Gila::config('media_uploads') ?? 'assets';
    if (FS_ACCESS) {
      $allowedPaths = array_merge($allowedPaths, ['src','themes']);
    }
    if (!is_dir($path)) {
      $path = pathinfo($path)['dirname'];
    }
    $path = substr(realpath($path), strlen(self::$sitepath)+1);

    foreach ($allowedPaths as $allowed) {
      if (substr($path, 0, strlen($allowed)+1) === $allowed.'/' ||
          $path === $allowed) {
        return true;
      }
    }
    return false;
  }
}
