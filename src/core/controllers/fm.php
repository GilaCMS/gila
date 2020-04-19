<?php


class fm extends controller
{
  public $path;
  private $relativePath;
  private $sitepath;

  function __construct ()
  {
    if(!gila::hasPrivilege('admin')
     && !gila::hasPrivilege('upload_assets')
     && !gila::hasPrivilege('edit_assets')) exit;
   $this->sitepath = realpath(__DIR__.'/../../../'.SITE_PATH);
   $this->setPaths();
  }

  function indexAction ()
  {
      //view::renderAdmin('admin/fm.php');
  }

  function dirAction ()
  {
    if (!$this->allowedPath()) {
      die("Permission denied");
    }
    $files = scandir($this->path);

    if($this->allowedPath($this->path.'/..')) {
      $filelist[] = ['name'=>'..','size'=>0,'mtime'=>'','mode'=>'','ext'=>''];
    }

    foreach ($files as $file) if(($file!='.')&&($file!='..')) {
      if (is_file($file)) {
        $icon = 'folder';
        $stat = stat($this->relativePath.'/'.$file);
      }
      else {
        $icon = 'fa fa-folder';
        $stat = stat($this->relativePath.'/'.$file);
      }
      $this->pathinfo = pathinfo($file);
      if(is_file($file)) $extension = '.'; else $extension = '';
      if(isset($this->pathinfo['extension'])) $extension = $this->pathinfo['extension'];

      $newfile = array('name'=>$file,'size'=>$stat['size'],'mtime'=>date("Y-m-d H:i:s", $stat['mtime']),'mode'=>$stat['mode'],'ext'=>$extension);
      $filelist[] = $newfile;
    }

    $folderinfo['files'] = $filelist;
    $folderinfo['path'] = $this->relativePath;
    echo json_encode($folderinfo);
  }

  function readAction () {
    if (!$this->allowedPath() || !$this->allowedFiletype($this->path)) {
      die("Permission denied");
    }
    if (!gila::hasPrivilege('admin')) exit; 
    if (!is_file($this->path)) die("Path is not a file");
    echo htmlspecialchars(file_get_contents($this->path));
  }

  function saveAction () {
    if(!$this->allowedFiletype($this->path)) {
      die("You cannot edit this file type.");
    }
    if (!$this->allowedPath($this->relativePath)) {
      die("Permission denied.");
    }
    if(!file_put_contents($this->path, $_POST['contents'])) {
      ob_clean();
      die("Permission denied.");
    }
    die("File saved successfully");
  }

  function newfolderAction () {
    if (!$this->allowedPath($_POST['path'])) {
      die("Permission denied.");
    }
    mkdir(SITE_PATH.str_replace('..','',$_POST['path']),0755,true);
    die("Folder created successfully");
  }

  function newfileAction () {
    if(!$this->allowedPath($this->relativePath)) {
      die("Permission denied.");
    }
    file_put_contents(SITE_PATH.str_replace('..','',$_POST['path']),' ');
    die("File created successfully");
  }

  function moveAction () {
    if(!is_dir($this->path)) {
      if(!$this->allowedFiletype($this->path) ||
         !$this->allowedFiletype($_POST['newpath'])) {
        die("File type is not permited");
      }
    }
    if (!$this->allowedPath($_POST['newpath']) ||
        !$this->allowedPath($this->relativePath)) {
      die("Permission denied1");
    }
    if(!gila::hasPrivilege('admin') && !gila::hasPrivilege('edit_assets')) {
      die("User dont have permision to edit files");
    }

    if(!rename($this->path, $_POST['newpath'])) {
      ob_clean();
      die("File could not be moved");
    }
    die("File saved successfully");
  }

  function uploadAction() {
    if (!$this->allowedPath($this->relativePath)) {
      die("Permission denied.");
    }
    if(!gila::hasPrivilege('admin') && !gila::hasPrivilege('upload_assets')) {
      die("Permission denied.");
    }
    if(!isset($_FILES['uploadfiles'])) {
      die("Error: could not upload file!");
    }
    if (isset($_FILES['uploadfiles']["error"]) && $_FILES['uploadfiles']["error"] > 0) {
      echo "Error: " . $_FILES['uploadfiles']['error'] . "<br>";
    }
    $path = $this->relativePath;
    $tmp_file = $_FILES['uploadfiles']['tmp_name'];
    $name = $_FILES['uploadfiles']['name'];
    if (!is_array($tmp_file)) {
      $tmp_file = [$tmp_file];
      $name = [$name];
    }
    for ($i=0; $i<count($tmp_file); $i++) {
      if (!$this->allowedFiletype($name[$i])) {
        die("Error: File type {$name[$i]} is not accepted!");
      }
      if (!move_uploaded_file($tmp_file[$i], SITE_PATH.$path.'/'.$name[$i])) {
        die("Error: could not upload file!");
      }
    }

    echo "File uploaded successfully";
  }

  function deleteAction () {
    if (!$this->allowedPath($this->relativePath)) {
      die("Permission denied.");
    }
    if(!gila::hasPrivilege('admin') && !gila::hasPrivilege('edit_assets')) {
      die("Permission denied.");
    }
    if(!unlink($this->path) && !rmdir($this->path)){
      ob_clean();
      echo "File could not be deleted.";
    }
  }

  function efileAction () {
    $fileOut=realpath($_GET['f']);
    $ext = explode('.',$fileOut);
    $ext = $ext[count($ext)-1];
    if(isset($_GET['thumb'])) {
      $fileOut = view::thumb($fileOut, 'media_thumb/', (int)$_GET['media_thumb']);
    }

    if (file_exists($fileOut)) {
      $imageInfo = getimagesize($fileOut);
      switch ($imageInfo[2]) {
        case IMAGETYPE_JPEG:
          header("Content-Type: image/jpeg");
          break;
        case IMAGETYPE_GIF:
          header("Content-Type: image/gif");
          break;
        case IMAGETYPE_PNG:
          header("Content-Type: image/png");
          break;
        default:
          if($ext=='svg') echo file_get_contents($fileOut);
          exit;
          break;
      }

      header('Content-Length: ' . filesize($fileOut));
      readfile($fileOut);
    } else {
      http_response_code(404);
    }
  }

  function allowedPath($path = null) {
    $allowedPaths = ['assets','tmp','log'];
    if(FS_ACCESS) $allowedPaths = array_merge($allowedPaths, ['src','themes']);
    if ($path===null) {
      $path = $this->relativePath;
    } else {
      if(!is_dir($path)) $path = pathinfo($path)['dirname'];
      $path = substr(realpath($path), strlen($this->sitepath)+1);
    }

    foreach ($allowedPaths as $allowed) {
      if (substr($path,0,strlen($allowed)+1) == $allowed.'/' ||
          $path == $allowed) {
        return true;
      }
    }
    return false;
  }

  function allowedFiletype($path)
  {
    $allowedFiletypes = [
      'txt','json','css','pdf','twig','csv','tsv','log','html',
      'png','jpg','jpeg','gif','webp','ico',
      'avi','webm','mp4','mkv','ogg'
    ];
    if(is_dir($path)) return true;
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if(in_array($ext, $allowedFiletypes)) {
      return true;
    }
    return false;
  }

  function setPaths()
  {
    $this->path = $this->sitepath;
    if (isset($_GET['path']))  if(!$_GET['path']=='') $this->path = str_replace('\\','/',$_GET['path']);
    if (isset($_POST['path']))  if(!$_POST['path']=='') $this->path = str_replace('\\','/',$_POST['path']);
    $this->path = realpath($this->path);
    $base = substr($this->path, 0, strlen($this->sitepath));
    if($base != $this->sitepath) $this->path = $this->sitepath;
    $this->relativePath = substr($this->path, strlen($this->sitepath)+1);
  }

}
