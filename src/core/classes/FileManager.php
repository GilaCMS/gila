<?php

namespace Gila;

class FileManager
{
  public static $sitepath = __DIR__;
  const ILLEGAL_CHARACTERS = ['%','&','<','>','\\','{','}','*','?','\'','"',':','`','$','@','!','=','+','|'];

  public static function copy($source, $target, $rnd=false)
  {
    if ($rnd) {
      $target = self::genName($source, $target);
    }
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
    return $target;
  }

  public static function genName($src, $target)
  {
    $pathinfo = pathinfo($src);
    $ext = $pathinfo['extension'] ? '.'.$pathinfo['extension'] : '';
    do {
      $basename = substr(bin2hex(random_bytes(30)), 0, 30);
      $file = $target.'/'.$basename.$ext;
    } while (strlen($basename) < 30 || file_exists($file));
    return $file;
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
    Cache::set('fsize', function () {
      return FileManager::getUploadsSize();
    });
  }

  public static function allowedFileType($path)
  {
    $filetypes = [
      'txt','json','css','pdf','twig','csv','tsv','log',
      'png','jpg','jpeg','gif','webp','ico',
      'avi','webm','mp4','mkv','ogg','mp3'
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
    if (Config::get('media_uploads')) {
      $allowedPaths[] = Config::get('media_uploads');
    }

    if (!is_dir($path)) {
      $path = pathinfo($path)['dirname'];
    }
    if ($read && (strpos($path, 'src/')===0 || strpos($path, 'themes/')===0
      || strpos($path, 'assets/')===0) && strpos($path, 'assets/uploads')!==0) {
      $allowedPaths = ['src', 'themes', 'assets'];
      $path = substr(realpath($path), strlen(realpath('.'))+1);
    } else {
      if (!empty(SITE_PATH) && strpos($path, 'sites/')!==0) {
        $path = SITE_PATH.'/'.$path;
      }
      $path = substr(realpath($path), strlen(realpath(self::$sitepath))+1);
    }

    foreach ($allowedPaths as $allowed) {
      if (substr($path, 0, strlen($allowed)+1) === $allowed.'/' ||
          $path === $allowed) {
        return true;
      }
    }

    return false;
  }

  public static function getUploadsSize()
  {
    $path = Config::get('media_uploads') ?? 'assets/uploads';
    return self::getDirectorySize($path);
  }

  public static function getDirectorySize($path)
  {
    $bytestotal = 0;
    if (self::allowedPath($path, true) && file_exists($path)) {
      if (!empty(SITE_PATH) && strpos($path, 'sites/')!==0) {
        $path = SITE_PATH.'/'.$path;
      }
      $path = realpath($path);
      foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $file) {
        $bytestotal += $file->getSize();
      }
    }
    return $bytestotal;
  }

  public static function uploadError($id)
  {
    $phpFileUploadErrors = [
      0 => 'There is no error, the file uploaded with success',
      1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
      2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
      3 => 'The uploaded file was only partially uploaded',
      4 => 'No file was uploaded',
      6 => 'Missing a temporary folder',
      7 => 'Failed to write file to disk.',
      8 => 'A PHP extension stopped the file upload.',
    ];
    return $phpFileUploadErrors[$id]??$id;
  }
}
