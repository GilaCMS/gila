<?php

namespace Gila;

class FileManager
{
  public static $sitepath = __DIR__;

  public static function copy($source, $target)
  {
    if (is_dir($source)) {
      $target = Config::dir($target);
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
    if (Config::get('allow_filetypes') && Session::hasPrivilege('admin')) {
      $filetypes = merge_array($filetypes, Config::get('allow_filetypes'));
    }
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if (in_array($ext, $filetypes)) {
      return true;
    }
    return false;
  }

  public static function allowedPath($path, $read=false)
  {
    $allowedPaths = ['data/public','tmp','assets'];
    if (Session::hasPrivilege('admin')) {
      $allowedPaths[] = 'log';
    }
    $allowedPaths[] = Config::get('media_uploads') ?? 'assets';
    if (!is_dir($path)) {
      $path = pathinfo($path)['dirname'];
    }
    if($read && (strpos($path, 'src/')===0 || strpos($path, 'themes/')===0
      || strpos($path, 'assets/')===0)) {
      $path = substr(realpath($path), strlen(realpath('.'))+1);
      $allowedPaths = ['src', 'themes', 'assets'];
    } else {
      $path = substr(realpath($path), strlen(self::$sitepath)+1);
    }

    if (empty($path)) {
      return false;
    }
    foreach ($allowedPaths as $allowed) {
      if (substr($path, 0, strlen($allowed)+1) === $allowed.'/' ||
          $path === $allowed) {
        return true;
      }
    }
    return false;
  }
}
