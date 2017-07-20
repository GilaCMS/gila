<?php

class admin extends controller
{
    /*public $icons = [
        'index'=>'dashboard',
        'addons'=>'dropbox',
        'posts'=>'pencil',
        'users'=>'users',
        'settings'=>'cogs',
        'widgets'=>'th-large',
    ];*/

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
                //echo "<h2>Edit widget #".$r['id']."</h2>";
                /*view::set('title',$r['title']);*/
                view::set('widget',$r);
                view::renderFile('../widgets/'.$r->widget.'/edit.phtml');
                view::renderFile('admin/edit_widget.phtml');
            }

            return;
        }
        view::set('page', (router::get('page',1)?:1));
        view::set('rpp', 10);
        view::renderAdmin('admin/list_widget.phtml');
    }

    function update_widgetAjax ()
    {
        global $db;
        echo $_POST['widget_data'];
        //echo $_POST['widget_id'];

        if (isset($_POST['widget_data'])) {
            $db->query("UPDATE widget SET data=?,area=? WHERE id=?",[$_POST['widget_data'],$_POST['widget_area'],$_POST['widget_id']]);
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
      view::renderAdmin('admin/addons.phtml');
    }

    function settingsAdmin ()
    {
        view::renderAdmin('admin/settings.phtml');
    }
    function loginAdmin ()
    {
        view::renderAdmin('login.phtml');
    }
    function logoutAdmin ()
    {
        session::destroy();
    }

    function media_uploadAction(){
        if(isset($_FILES['uploadfiles'])) {
            if (isset($_FILES['upload_files']["error"])) if ($_FILES['upload_files']["error"] > 0) {
                echo "Error: " . $_FILES['upload_files']['error'] . "<br>";
            }
            $path = router::post('path','assets');
            if(!move_uploaded_file($_FILES['uploadfiles']['tmp_name'],$path.'/'.$_FILES['uploadfiles']['name'].'.jpg')) {
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
