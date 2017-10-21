<?php

use core\models\widget;

class admin extends controller
{

    function __construct ()
    {
        if(session::key('user_id')==0) {
            view::renderFile('login.php');
            exit;
        }
    }

    function indexAction ()
    {
      global $posts,$pages,$users,$packages;
      global $db;
      $posts = $db->value('SELECT count(*) from post;');
      $pages = $db->value('SELECT count(*) from page;');
      $users = $db->value('SELECT count(*) from user;');
      $packages = count($GLOBALS['config']['packages']);
      view::renderAdmin('admin/dashboard.phtml');
    }

    function postsAction ()
    {
        global $db;
        if ($id = router::get('id',1)) {
            view::set('id',$id);
            view::script('src/core/assets/admin/media.js');
            view::renderAdmin('admin/edit_post.phtml');
            return;
        }
        view::renderAdmin('admin/post.phtml');
    }

    function pagesAction ()
    {
        global $db;
        if ($id = router::get('id',1)) {
            view::set('id',$id);
            view::renderAdmin('admin/edit_page.phtml');
            return;
        }
        view::renderAdmin('admin/page.phtml');
    }

    function postcategoriesAction ()
    {
        view::renderAdmin('admin/postcategory.phtml');
    }

    function widgetsAction ()
    {
        global $db;

        if ($id = router::get('id',1)) {
            view::set('widget',widget::getById($id));
            view::renderFile('admin/edit_widget.php');
            return;
        }
        view::renderAdmin('admin/list_widget.php');
    }

    function update_widgetAjax ()
    {
        global $db;
        $widget_data =json_encode($_POST['option']);
        echo $widget_data;
        if (isset($_POST['option'])) {
            $db->query("UPDATE widget SET data=?,area=?,pos=?,title=? WHERE id=?",[$widget_data,$_POST['widget_area'],$_POST['widget_pos'],$_POST['widget_title'],$_POST['widget_id']]);
            echo $_POST['widget_id'];
        }
    }

    function usersAction ()
    {
        view::renderAdmin('admin/user.phtml');
    }

    function packagesAction ()
    {
        if(!$contents=file_get_contents('http://gilacms.com/packages/')) {
            echo "<br>Could not connect to packages list. Please try later.";
            exit;
        }
        $packages = json_decode($contents);
        view::set('packages',$packages);
        view::renderFile('admin/packages.php');
    }

    function newthemesAction ()
    {
        if(!$contents=file_get_contents('http://gilacms.com/packages/themes')) {
            echo "<br>Could not connect to themes list. Please try later.";
            exit;
        }
        $packages = json_decode($contents);
        view::set('packages',$packages);
        view::renderFile('admin/newthemes.php');
    }

    function addonsAction ()
    {
        view::script('src/core/assets/admin/media.js');
		view::renderAdmin('admin/addons.php');
    }

    function themesAction ()
    {
        view::script('src/core/assets/admin/media.js');
		view::renderAdmin('admin/themes.php');
    }

    function settingsAction ()
    {
        view::script('src/core/assets/admin/media.js');
        view::renderAdmin('admin/settings.php');
    }

    function loginAction ()
    {
        view::renderAdmin('login.phtml');
    }

    function logoutAction ()
    {
        global $db;
        //if(isset($_COOKIE['GSESSIONID']))
            $res = $db->query("DELETE FROM usermeta WHERE user_id=? AND vartype='GSESSIONID';",[session::key('user_id')]);
        session::destroy();
        echo "<meta http-equiv='refresh' content='0;url=".gila::config('base')."' />";
    }

    function media_uploadAction(){
        if(isset($_FILES['uploadfiles'])) {
            if (isset($_FILES['upload_files']["error"])) if ($_FILES['upload_files']["error"] > 0) {
                echo "Error: " . $_FILES['upload_files']['error'] . "<br>";
            }
            $path = router::post('path','assets');
            if(!move_uploaded_file($_FILES['uploadfiles']['tmp_name'],$path.'/'.$_FILES['uploadfiles']['name'])) {
                echo "Error: could not upload file!<br>";
            }
        }
        self::mediaAction();
    }

    function mediaAction()
    {
        view::script('src/core/assets/admin/media.js');
        view::renderAdmin('admin/media.php');
    }

    function db_backupAction()
    {
        view::includeFile('admin/header.php');
        new db_backup();
        view::includeFile('admin/footer.php');
    }

    function updateAction()
    {
        $zip = new ZipArchive;
        $target = 'src/core';
        $file = 'http://gilacms.com/assets/packages/core'.$download.'.zip';
        $localfile = 'src/core.zip';
        if (!copy($file, $localfile)) {
          echo "Failed to download new version!";
        }
        if ($zip->open($localfile) === TRUE) {
          if(!file_exists($target)) mkdir($target);
          $zip->extractTo($target);
          $zip->close();
          include 'src/core/update.php';
          echo 'Gila CMS successfully updated to '.$version;
        } else {
          echo 'Failed to download new version!';
        }
    }

}
