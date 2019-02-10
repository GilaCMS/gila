<?php


class fm extends controller
{
    public $path;

    function __construct ()
    {
        if(!gila::hasPrivilege('admin')) exit;
        $dpath = realpath(__DIR__.'/../../../');
        $this->path = $dpath;
        if (isset($_GET['path']))  if(!$_GET['path']=='') $this->path = str_replace('\\','/',$_GET['path']);
        if (isset($_POST['path']))  if(!$_POST['path']=='') $this->path = str_replace('\\','/',$_POST['path']);
        $this->path = realpath($this->path);
        if(strlen($dpath)>strlen($this->path)) $this->path = $dpath;
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
        if (!is_file($this->path)) die("Path is not a file");
        echo htmlspecialchars(file_get_contents($this->path));
    }

    function saveAction () {
        if(!file_put_contents($this->path,$_POST['contents'])){
            ob_clean();
            echo "Permission denied.";
        }
    }

    function newfolderAction () {
        mkdir($_POST['path'],0755,true);
    }

    function newfileAction () {
        file_put_contents($_POST['path'],' ');
    }

    function moveAction () {
        if(!rename($this->path,$_POST['newpath'])){
            ob_clean();
            echo "Permission denied.";
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
        }
    }
}
