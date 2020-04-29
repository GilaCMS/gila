<?php


class fm extends controller
{
  public $path;
  private $relativePath;
  private $sitepath;

  function __construct ()
  {
    if(!Gila::hasPrivilege('admin')
     && !Gila::hasPrivilege('upload_assets')
     && !Gila::hasPrivilege('edit_assets')) exit;
   $this->sitepath = realpath(__DIR__.'/../../../'.SITE_PATH);
   $this->setPaths();
  }

  function indexAction ()
  {
      //View::renderAdmin('admin/fm.php');
  }

  function dirAction ()
  {
    if (!FileManager::allowedPath()) {
      die("Permission denied");
    }
    $files = scandir($this->path);

    if(FileManager::allowedPath($this->path.'/..')) {
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
    if (!FileManager::allowedPath() || !FileManager::allowedFileType($this->path)) {
      die("Permission denied");
    }
    if (!Gila::hasPrivilege('admin')) exit; 
    if (!is_file($this->path)) die("Path is not a file");
    echo htmlspecialchars(file_get_contents($this->path));
  }

  function saveAction () {
    if(!FileManager::allowedFileType($this->path)) {
      die("You cannot edit this file type.");
    }
    if (!FileManager::allowedPath($this->relativePath)) {
      die("Permission denied.");
    }
    if(!file_put_contents($this->path, $_POST['contents'])) {
      ob_clean();
      die("Permission denied.");
    }
    die("File saved successfully");
  }

  function newfolderAction () {
    if (!FileManager::allowedPath($_POST['path'])) {
      die("Permission denied.");
    }
    mkdir(SITE_PATH.str_replace('..','',$_POST['path']),0755,true);
    die("Folder created successfully");
  }

  function newfileAction () {
    if(!FileManager::allowedPath($this->relativePath)) {
      die("Permission denied.");
    }
    file_put_contents(SITE_PATH.str_replace('..','',$_POST['path']),' ');
    die("File created successfully");
  }

  function moveAction () {
    if(!is_dir($this->path)) {
      if(!FileManager::allowedFileType($this->path) ||
         !FileManager::allowedFileType($_POST['newpath'])) {
        die("File type is not permited");
      }
    }
    if (!FileManager::allowedPath($_POST['newpath']) ||
        !FileManager::allowedPath($this->relativePath)) {
      die("Permission denied1");
    }
    if(!Gila::hasPrivilege('admin') && !Gila::hasPrivilege('edit_assets')) {
      die("User dont have permision to edit files");
    }

    if(!rename($this->path, $_POST['newpath'])) {
      ob_clean();
      die("File could not be moved");
    }
    die("File saved successfully");
  }

  function uploadAction() {
    if (!FileManager::allowedPath($this->relativePath)) {
      die("Permission denied.");
    }
    if(!Gila::hasPrivilege('admin') && !Gila::hasPrivilege('upload_assets')) {
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
      if (!FileManager::allowedFileType($name[$i])) {
        die("Error: File type {$name[$i]} is not accepted!");
      }
      if (!move_uploaded_file($tmp_file[$i], SITE_PATH.$path.'/'.$name[$i])) {
        die("Error: could not upload file!");
      }
    }

    echo "File uploaded successfully";
  }

  function deleteAction () {
    if (!FileManager::allowedPath($this->relativePath)) {
      die("Permission denied.");
    }
    if(!Gila::hasPrivilege('admin') && !Gila::hasPrivilege('edit_assets')) {
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
      $fileOut = View::thumb($fileOut, 'media_thumb/', (int)$_GET['media_thumb']);
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
