<?php

class admin extends controller
{

    function indexAdmin ()
    {
      global $posts,$pages,$users,$packages;
      global $db;
      $posts = $db->value('SELECT count(*) from post;');
      $pages = $db->value('SELECT count(*) from page;');
      $users = $db->value('SELECT count(*) from user;');
      $packages = count($GLOBALS['config']['packages']);
      view::renderAdmin('admin/dashboard.phtml');
    }

    function postsAdmin ()
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

    function pagesAdmin ()
    {
        global $db;
        if ($id = router::get('id',1)) {
            view::set('id',$id);
            view::renderAdmin('admin/edit_page.phtml');
            return;
        }
        view::renderAdmin('admin/page.phtml');
    }

    function postcategoriesAdmin ()
    {
        view::renderAdmin('admin/postcategory.phtml');
    }

    function widgetsAdmin ()
    {
        global $db;

        if ($id = router::get('id',1)) {
            $res = $db->query("SELECT * FROM widget WHERE id=?",$id);
            if ($r = mysqli_fetch_object($res)) {
                view::set('widget',$r);
                view::renderFile('admin/edit_widget.php');
            }
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

    function usersAdmin ()
    {
        global $db;
        if ($id = router::get('id',1)) {
            echo "Edit user ".$id;
            exit;
        }
        view::renderAdmin('admin/user.phtml');
    }


    function addonsAdmin ()
    {
		view::renderAdmin('admin/addons.php');
    }

    function settingsAdmin ()
    {
        view::renderAdmin('admin/settings.php');
    }
    function loginAdmin ()
    {
        view::renderAdmin('login.phtml');
    }
    function logoutAdmin ()
    {
        global $db;
        session::destroy();
        if(isset($_COOKIE['GSESSIONID'])) {
           $res = $db->query("DELETE FROM usermeta WHERE value=? AND vartype='GSESSIONID';",[$_COOKIE['GSESSIONID']]);
       }
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

}
