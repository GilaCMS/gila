<?php

namespace Gila;

class Image
{
  /**
   * Creates a thumbnail image from a file.
   * @param $src (string) Path to the original image
   * @param $file (string) Path to the thumbnail to create
   * @param $max_width (int) Maximun width in pixels for the new image
   * @param $max_height (int) Maximun height in pixels for the new image
   * @return boolean True if the thumbnail is created successfully
   */
  public static function makeThumb($src, $file, $max_width, $max_height, $img_type=null)
  {
    $src_width = 0;
    $src_height = 0;
    $ext = pathinfo($src)['extension'] ?? null;
    if (!self::imageExtention($ext)) {
      FileManager::$sitepath = realpath(SITE_PATH);
      if ($ext=='svg' && FileManager::allowedPath($src)) {
        Config::dir(substr($file, 0, strrpos($file, '/')));
        copy($src, $file);
        return true;
      }
      return false;
    }
    $src = self::localPath($src);
    if ($src === false) {
      return false;
    }
    
    Config::dir(substr($file, 0, strrpos($file, '/')));

    if ($image = getimagesize($src)) {
      list($src_width, $src_height)=$image;
      if ($img_type!==32) {
        $img_type = $image[2];
      }
    }

    $newwidth=$max_width;
    $newheight=$max_height;

    if ($src_width>$max_width) {
      $newheight=($src_height/$src_width)*$newwidth;
    } elseif ($src_height>$max_height) {
      $newwidth=($src_width/$src_height)*$newheight;
    } elseif ($img_type != 2) {
      copy($src, $file);
      return true;
    } else {
      $newwidth=$src_width;
      $newheight=$src_height;
    }

    $tmp = self::createTmp($newwidth, $newheight, $image[2]);
    $img_src = self::create($src, $image[2]);

    imagecopyresampled($tmp, $img_src, 0, 0, 0, 0, $newwidth, $newheight, $src_width, $src_height);
    self::save($tmp, $file, $img_type);
    imagedestroy($img_src);
    imagedestroy($tmp);
    return true;
  }

  /**
   * Creates an image depends the type.
   * @param $src (string) Path to the original image
   * @param $type (int) Original image type
   */
  public static function create($src, $type = 2)
  {
    if ($type === 1) {
      return imageCreateFromGIF($src);
    }
    if ($type === 2) {
      return imageCreateFromJPEG($src);
    }
    if ($type === 3) {
      return imageCreateFromPNG($src);
    }
    if ($type === 32) {
      return imageCreateFromWebp($src);
    }

    return imageCreateFromJPEG($src);
  }

  public static function createTmp($width, $height, $type = 2)
  {
    $tmp = imagecreatetruecolor($width, $height);
    if ($type === 3 || $type === 32) {
      $color = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
      imagecolortransparent($tmp, $color);
      imagefill($tmp, 0, 0, $color);
    }
    return $tmp;
  }

  /**
   * Saves an image.
   * @param $src (string) Path to the original image
   * @param $file (string) Path to the target image
   * @param $type (int) Original image type
   */
  public static function save($tmp, $file, $type = 2)
  {
    switch ($type) {
    case 1:
      imagegif($tmp, $file);
      break;
    case 2:
      imageinterlace($tmp, 1);
      imagejpeg($tmp, $file, 90);
      break;
    case 3:
      imagesavealpha($tmp, true);
      imagepng($tmp, $file, 6);
      break;
    case 32:
      imagewebp($tmp, $file, 90);
      break;
    }
  }

  /**
   * Creates a stacked image from many files
   * @param $revision  The revision string to add after the file name
   * @param $src_array (Array string) Paths to the original images
   * @param $file (string) Path to the stacked thumbnail to create
   * @param $max_width (int) Maximun width in pixels for the new images
   * @param $max_height (int) Maximun height in pixels for the new images
   * @return Array
   */
  public static function makeStack($revision, $src_array, $file, $max_width, $max_height)
  {
    $response = [];
    $dst_y = 0;
    $total_y = 0;

    foreach ($src_array as $key=>$src) {
      $ext = pathinfo($src)['extension'] ?? null;
      if (!self::imageExtention($ext)) {
        continue;
      }
      $_src = self::localPath($src);
      if ($_src === false) {
        $_src = $src;
      }
      Config::dir(substr($file, 0, strrpos($file, '/')));

      if ($image = @getimagesize($_src)) {
        list($src_width, $src_height, $src_type) = $image;
        if ($src_type!=2) {
          $response[$key] = false;
          continue;
        }
        $newwidth=$max_width;
        $newheight=$max_height;

        if ($src_width>$max_width) {
          $newheight = ($src_height/$src_width)*$newwidth;
        } elseif ($src_height>$max_height) {
          $newwidth = ($src_width/$src_height)*$newheight;
        }

        $total_y += $newheight;
        $response[$key]=[
          'src' => $src,
          'src_width' => $src_width,
          'src_height' => $src_height,
          'width' => (int)$newwidth,
          'height' => (int)$newheight,
          'type' => $src_type
        ];
      } else {
        $response[$key] = false;
      }
    }

    $tmp = self::createTmp($max_width, $total_y, 3);

    foreach ($response as $key=>$img) {
      if ($img) {
        $src = $src_array[$key];
        $img_src = self::create($src, $img['type']);
        imagecopyresampled($tmp, $img_src, 0, $dst_y, 0, 0, $img['width'], $img['height'], $img['src_width'], $img['src_height']);
        $response[$key]['top'] = $dst_y;
        $dst_y += $img['height'];
        imagedestroy($img_src);
      }
    }

    self::save($tmp, $file);
    imagedestroy($tmp);
    file_put_contents($file.'.json', json_encode([$revision,$response]));
    return [$file.'?'.$revision, $response];
  }

  public static function localPath($src)
  {
    if (strpos($src, Config::get('base')) === 0) {
      return $src;
    }
    if (strpos($src, 'https://') !== 0) {
      return realpath($src);
    }
    $_src = TMP_PATH.'/'.str_replace(["://",":\\\\","\\","/",":"], "_", $src);
    if (!file_exists($_src)) {
      $_file = LOG_PATH.'/cannot_copy.json';
      $cannot_copy = json_decode(file_get_contents($_file), true);
      if (in_array($src, $cannot_copy)) {
        return false;
      }
      if (!copy($src, $_src)) {
        $cannot_copy[] = $src;
        file_put_contents($_file, json_encode($cannot_copy, JSON_PRETTY_PRINT));
        return false;
      }
    }
    return $_src;
  }

  public static function imageExtention($ext)
  {
    if (in_array($ext, ['gif','png','jpg','jpeg','webp','tiff','tif'])) {
      return true;
    }
    return false;
  }

  public static function readfile($file)
  {
    $file = strtr($file, ['..'=>'']);
    if (file_exists($file)) {
      ob_end_clean();
      header('Content-Length: '.filesize($file));
      $ext = explode('.', $file);
      $ext = strtolower($ext[count($ext)-1]);
      if ($imageInfo = getimagesize($file)) {
        $extType = [2=>'jpeg',32=>'webp',3=>'png',1=>'gif'];
        $ext = $extType[$imageInfo[2]] ?? $ext;
      }
      FileManager::$sitepath = realpath(SITE_PATH);
      if (in_array($ext, ['jpeg','jpg','png','gif','webp'])
      && FileManager::allowedPath($file, true)) {
        header("Content-Type: image/".$ext);
        readfile($file);
      } elseif ($ext==='svg' &&
          (strpos($file, SITE_PATH.'assets/')==0 || substr($file, 0, 4) == 'src/')) {
        header("Content-Type: image/svg+xml");
        echo file_get_contents($file);
      } else {
        http_response_code(404);
      }
    } else {
      http_response_code(404);
    }
  }
}
