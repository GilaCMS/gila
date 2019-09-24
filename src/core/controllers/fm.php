<?php


class fm extends controller
{
  public $path;

  function __construct ()
  {
    if(!FS_ACCESS) exit;
    if(!gila::hasPrivilege('admin')) exit;
    $dpath = realpath(__DIR__.'/../../../'.SITE_PATH);
    $this->path = $dpath;
    if (isset($_GET['path']))  if(!$_GET['path']=='') $this->path = str_replace('\\','/',$_GET['path']);
    if (isset($_POST['path']))  if(!$_POST['path']=='') $this->path = str_replace('\\','/',$_POST['path']);
    $this->path = realpath($this->path);
    $base = substr($this->path, 0, strlen($dpath));
    if($base != $dpath) $this->path = $dpath;
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
    if (!gForm::posted()) die("Permission denied.");
    if (!is_file($this->path)) die("Path is not a file");
    echo htmlspecialchars(file_get_contents($this->path));
  }

  function saveAction () {
    if(!gForm::posted() || !file_put_contents($this->path, $_POST['contents'])) {
      ob_clean();
      echo "Permission denied.";
    }
  }

  function newfolderAction () {
    if(!gForm::posted()) {
      echo "Permission denied.";
      return;
    }
    mkdir(SITE_PATH.str_replace('..','',$_POST['path']),0755,true);
  }

  function newfileAction () {
    if(!gForm::posted()) {
      echo "Permission denied.";
      return;
    }
    file_put_contents(SITE_PATH.str_replace('..','',$_POST['path']),' ');
  }

  function moveAction () {
    if(!gForm::posted() || !rename($this->path, $_POST['newpath'])) {
      ob_clean();
      echo "Permission denied.";
    }

  }

  function uploadAction() {
    if(isset($_FILES['uploadfiles']) && gForm::posted()) {
      if (isset($_FILES['uploadfiles']["error"])) if ($_FILES['uploadfiles']["error"] > 0) {
        echo "Error: " . $_FILES['uploadfiles']['error'] . "<br>";
      }
      $path = router::post('path','');
      if($path[0]=='.') $path='assets';
      $tmp_file = $_FILES['uploadfiles']['tmp_name'];
      $name = $_FILES['uploadfiles']['name'];
      if(is_array($tmp_file)) {
        for($i=0;i<count($tmp_file);$i++) {
          if(!move_uploaded_file($tmp_file[$i], SITE_PATH.$path.'/'.$name[$i])) {
            echo "Error: could not upload file!<br>";
          }
        }
      }else{
        if(!move_uploaded_file($tmp_file, SITE_PATH.$path.'/'.$name)) {
          echo "Error: could not upload file!<br>".$path.'/'.$name;
        }
      }
    }
  }

  function deleteAction () {
    if(!unlink($this->path)){
      ob_clean();
      echo "Permission denied.";
    }
  }

  function efileAction () {
    $fileOut=realpath($_GET['f']);
    $ext = explode('.',$fileOut);
    $ext = $ext[count($ext)-1];
    if(isset($_GET['thumb'])) {
      $fileOut = view::thumb($fileOut,'media_thumb/', (int)$_GET['media_thumb']);
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
