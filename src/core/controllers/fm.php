<?php


class fm extends controller
{
  public $path;
  private $relativePath;

  function __construct ()
  {
    if(!gila::hasPrivilege('admin')
     && !gila::hasPrivilege('upload_assets')
     && !gila::hasPrivilege('edit_assets')) exit;
    $dpath = realpath(__DIR__.'/../../../'.SITE_PATH);
    $this->path = $dpath;
    if (isset($_GET['path']))  if(!$_GET['path']=='') $this->path = str_replace('\\','/',$_GET['path']);
    if (isset($_POST['path']))  if(!$_POST['path']=='') $this->path = str_replace('\\','/',$_POST['path']);
    $this->path = realpath($this->path);
    $base = substr($this->path, 0, strlen($dpath));
    if($base != $dpath) $this->path = $dpath;
    $this->relativePath = substr($this->path, strlen($dpath));
  }

  function indexAction ()
  {
      //view::renderAdmin('admin/fm.php');
  }

  function dirAction ()
  {
    $files = scandir($this->path);

    if($this->path!='') $filelist[] = ['name'=>'..','size'=>0,'mtime'=>'','mode'=>'','ext'=>''];

    foreach ($files as $file) if(($file!='.')&&($file!='..')) {
      if (is_file($file)) {
        $icon = 'folder';
        $stat = stat($this->path.'/'.$file);
      }
      else {
        $icon = 'fa fa-folder';
        $stat = stat($this->path.'/'.$file);
      }
      $this->pathinfo = pathinfo($file);
      if(is_file($file)) $extension = '.'; else $extension = '';
      if(isset($this->pathinfo['extension'])) $extension = $this->pathinfo['extension'];

      $newfile = array('name'=>$file,'size'=>$stat['size'],'mtime'=>date("Y-m-d H:i:s", $stat['mtime']),'mode'=>$stat['mode'],'ext'=>$extension);
      $filelist[] = $newfile;
    }

    $folderinfo['files'] = $filelist;
    $folderinfo['path'] = $this->path;
    echo json_encode($folderinfo);
  }

  function readAction () {
    if(!FS_ACCESS && !gila::hasPrivilege('admin')) exit; 
    if (!is_file($this->path)) die("Path is not a file");
    echo htmlspecialchars(file_get_contents($this->path));
  }

  function saveAction () {
    if(!FS_ACCESS || !file_put_contents($this->path, $_POST['contents'])) {
      ob_clean();
      die("Permission denied.");
    }
    die("File saved successfully");
  }

  function newfolderAction () {
    if(!FS_ACCESS && substr($this->relativePath,0,8)!='/assets/') exit;
    mkdir(SITE_PATH.str_replace('..','',$_POST['path']),0755,true);
    die("Folder created successfully");
  }

  function newfileAction () {
    if(!FS_ACCESS) {
      die("Permission denied.");
    }
    file_put_contents(SITE_PATH.str_replace('..','',$_POST['path']),' ');
    die("File created successfully");
  }

  function moveAction () {
    if(!FS_ACCESS && substr($this->relativePath,0,8)!='/assets/') {
      die("Permission denied.");
    }
    if(!gila::hasPrivilege('admin') && !gila::hasPrivilege('edit_assets')) {
      die("Permission denied.");
    }
    $ext = strtolower(pathinfo($this->path, PATHINFO_EXTENSION));
    $newext = strtolower(pathinfo($_POST['newpath'], PATHINFO_EXTENSION));
    if($ext != $newext || $newext == 'htaccess' || !rename($this->path, $_POST['newpath'])) {
      ob_clean();
      die("Permission denied.");
    }
    die("File saved successfully");
  }

  function uploadAction() {
    if(!FS_ACCESS && substr($this->relativePath,0,8)!='/assets/') {
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
    $path = router::post('path','');
    if($path[0]=='.') $path='assets';
    $tmp_file = $_FILES['uploadfiles']['tmp_name'];
    $name = $_FILES['uploadfiles']['name'];
    if(is_array($tmp_file)) {
      for($i=0;i<count($tmp_file);$i++) {
        if(!move_uploaded_file($tmp_file[$i], SITE_PATH.$path.'/'.$name[$i])) {
          die("Error: could not upload file!<br>");
        }
      }
    }else{
      if(!move_uploaded_file($tmp_file, SITE_PATH.$path.'/'.$name)) {
        die("Error: could not upload file!<br>".$path.'/'.$name);
      }
    }
    echo "File uploaded successfully";
  }

  function deleteAction () {
    if(!FS_ACCESS && substr($this->relativePath,0,8)!='/assets/') {
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
}
