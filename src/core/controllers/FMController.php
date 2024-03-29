<?php
use Gila\Session;
use Gila\FileManager;
use Gila\HtmlInput;

class FMController extends Gila\Controller
{
  public $path;
  private $relativePath;
  private $sitepath;

  public function __construct()
  {
    if (!Session::hasPrivilege('admin')
     && !Session::hasPrivilege('upload_assets')
     && !Session::hasPrivilege('edit_assets')) {
      exit;
    }
    $this->sitepath = realpath(__DIR__.'/../../../'.SITE_PATH);
    FileManager::$sitepath = $this->sitepath;
    $this->setPaths();
  }

  public function indexAction()
  {
    //View::renderAdmin('admin/fm.php');
  }

  public function dirAction()
  {
    if (!FileManager::allowedPath($this->relativePath)) {
      die("Permission denied");
    }
    $files = scandir($this->path);

    if (FileManager::allowedPath($this->path.'/..')) {
      $filelist[] = ['name'=>'..','size'=>0,'mtime'=>'','mode'=>'','ext'=>''];
    }

    foreach ($files as $file) {
      if (($file!='.')&&($file!='..')) {
        if (is_file($file)) {
          $icon = 'folder';
          $stat = stat($this->relativePath.'/'.$file);
        } else {
          $icon = 'fa fa-folder';
          $stat = stat($this->relativePath.'/'.$file);
        }
        $this->pathinfo = pathinfo($file);
        if (is_file($file)) {
          $extension = '.';
        } else {
          $extension = '';
        }
        if (isset($this->pathinfo['extension'])) {
          $extension = $this->pathinfo['extension'];
        }

        $newfile = [
        'name'=> str_replace(FileManager::ILLEGAL_CHARACTERS, '', $file),
        'size'=> $stat['size'],
        'mtime'=> date("Y-m-d H:i:s", $stat['mtime']),
        'mode'=> $stat['mode'], 'ext'=> $extension
      ];
        $filelist[] = $newfile;
      }
    }

    $folderinfo['files'] = $filelist;
    $folderinfo['path'] = $this->relativePath;
    echo json_encode($folderinfo);
  }

  public function readAction()
  {
    if (!FileManager::allowedPath($this->relativePath) ||
        !FileManager::allowedFileType($this->path)) {
      die("Permission denied");
    }
    if (!Session::hasPrivilege('admin')) {
      exit;
    }
    if (!is_file($this->path)) {
      die("Path is not a file");
    }
    echo htmlspecialchars(file_get_contents($this->path));
  }

  public function saveAction()
  {
    if (!FileManager::allowedFileType($this->path)) {
      die("You cannot edit this file type.");
    }
    if (!FileManager::allowedPath($this->relativePath)) {
      die("Permission denied.");
    }
    $contents = $_POST['contents'];
    if (!file_put_contents($this->path, $contents)) {
      ob_clean();
      die("Permission denied.");
    }
    die("File saved successfully");
  }

  public function newfolderAction()
  {
    $this->path = str_replace('..', '', $_POST['path']);
    if (!FileManager::allowedPath($this->path)) {
      die("Permission denied ".$this->path);
    }
    mkdir(SITE_PATH.$this->path, 0755, true);
    die("Folder created successfully");
  }

  public function newfileAction()
  {
    if (!FileManager::allowedFileType($_POST['path'])) {
      die("File type is not permited");
    }
    if (!FileManager::allowedPath($this->path)) {
      die("Permission denied.");
    }
    $path = htmlspecialchars(SITE_PATH.str_replace('..', '', $_POST['path']));
    file_put_contents($path, ' ');
    die("File created successfully");
  }

  public function moveAction()
  {
    if (!is_dir($this->path)) {
      if (!FileManager::allowedFileType($this->path) ||
         !FileManager::allowedFileType($_POST['newpath'])) {
        die("File type is not permited");
      }
    }
    if (!FileManager::allowedPath($_POST['newpath']) ||
        !FileManager::allowedPath($this->relativePath)) {
      die("Permission denied");
    }
    if ($_POST['newpath']!==str_replace(FileManager::ILLEGAL_CHARACTERS, '', $_POST['newpath'])) {
      die("Special characters are not accepted in filename!");
    }
    if (!Session::hasPrivilege('admin') && !Session::hasPrivilege('edit_assets')) {
      die("User dont have permision to edit files");
    }

    if (!rename($this->path, SITE_PATH.$_POST['newpath'])) {
      ob_clean();
      die("File could not be moved");
    }
    die("File saved successfully");
  }

  public function uploadAction()
  {
    if (!FileManager::allowedPath($_POST['path'])) {
      die("Incorrect path.");
    }
    if (!Session::hasPrivilege('admin') && !Session::hasPrivilege('upload_assets')) {
      die("Permission denied.");
    }
    if (!isset($_FILES['uploadfiles'])) {
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
      $name[$i] = pathinfo($name[$i], PATHINFO_FILENAME).'.'.strtolower(pathinfo($name[$i], PATHINFO_EXTENSION));
      if (file_exists(SITE_PATH.$path.'/'.$name[$i])) {
        die("Error: File with same name already exists!");
      }
      if (!FileManager::allowedFileType($name[$i])) {
        die("Error: File type is not accepted!");
      }
      if ($name[$i]!==str_replace(FileManager::ILLEGAL_CHARACTERS, '', $name[$i])) {
        die("Error: Special characters are not accepted in filename!");
      }

      if (!move_uploaded_file($tmp_file[$i], SITE_PATH.$path.'/'.$name[$i])) {
        die("Error: could not upload file!");
      }
    }

    echo "File uploaded successfully";
  }

  public function deleteAction()
  {
    if (!FileManager::allowedPath($_POST['path'])) {
      die("Incorecct path.");
    }
    if (!Session::hasPrivilege('admin') && !Session::hasPrivilege('edit_assets')) {
      die("Permission denied.");
    }
    if (!unlink($_POST['path']) && !rmdir($_POST['path'])) {
      ob_clean();
      echo "File could not be deleted.";
    }
  }

  public function setPaths()
  {
    $this->path = $this->sitepath;
    if (isset($_GET['path'])) {
      if (!$_GET['path']=='') {
        $this->path = strtr($_GET['path'], ['\\'=>'/']);
      }
    }
    if (isset($_POST['path'])) {
      if (!$_POST['path']=='') {
        $this->path = strtr($_POST['path'], ['\\'=>'/']);
      }
    }
    $this->path = realpath(SITE_PATH.$this->path);
    $base = substr($this->path, 0, strlen($this->sitepath));
    if ($base != $this->sitepath) {
      $this->path = $this->sitepath;
    }
    $this->relativePath = substr($this->path, strlen($this->sitepath)+1);
  }
}
