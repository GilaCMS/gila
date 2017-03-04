<?php

class admin extends controller
{
    public $icons = [
        'index'=>'dashboard',
        'addons'=>'dropbox',
        'posts'=>'pencil',
        'users'=>'users',
        'settings'=>'cogs',
        'widgets'=>'th-large',
    ];

    function indexAdmin ()
    {
        include 'src/core/views/admin/header.php';
      echo "Dashboard!";
      include 'src/core/views/admin/footer.php';
    }

    function postsAdmin ()
    {
        global $db;
        if ($id = router::get('id',1)) {
            if ($id == 'new') {
                view::set('page_title','New Post');
                view::set('id',$_POST['p_id']);
                view::set('title','');
                view::set('text','text');
                view::set('publish',1);
                view::renderAdmin('admin/edit_post.phtml');
                return;
            }
            $res = $db->query("SELECT * FROM post WHERE id=?",$id);
            while ($r = mysqli_fetch_array($res)) {
                view::set('page_title','Edit Post');
                view::set('id',$r['id']);
                view::set('title',$r['title']);
                view::set('text',$r['post']);
                view::set('publish',$r['publish']);
                view::renderAdmin('admin/edit_post.phtml');
            }

            return;
        }

        view::set('page_title','Posts');
        view::set('page', (router::get('page',1)?:1));
        view::set('rpp', 10);
        view::renderAdmin('admin/list_post.phtml');
        //echo "<table class=\"table\"><tr><th>ID<th>Title<th>Slug<th>User ID<th>Updated<th>";
        //$page = router::get('page',1)?:1;
        //$rpp = 10;
    }

    function widgetsAdmin ()
    {
        global $db;

        if ($id = router::get('id',1)) {
            $res = $db->query("SELECT * FROM widget WHERE id=?",$id);
            while ($r = mysqli_fetch_array($res)) {
                //echo "<h2>Edit widget #".$r['id']."</h2>";
                /*view::set('title',$r['title']);*/
                view::set('widget_id',$r['id']);
                view::renderAdmin('../widgets/'.$r['widget'].'/edit.phtml');
            }

            return;
        }
        include 'src/core/views/admin/header.php';

        echo "<table class=\"table\"><tr><th>ID<th>Widget<th>Area<th>Position<th>";

        $gen = $db->gen("SELECT * FROM widget");
        foreach ($gen as $r) {
            echo '<tr>'.'<td>'.$r['id'].'<td>'.$r['widget'].'<td>'.$r['area'].'<td>'.$r['pos'].'<td><a href="admin/widgets/'.$r['id'].'">Edit</a>';
        }
        echo "</table>";
        include 'src/core/views/admin/footer.php';
    }

    function update_widgetAjax ()
    {
        global $db;
        if (isset($_POST['widget_data'])) {
            $db->query("UPDATE widget SET data=? WHERE id=?",[$_POST['widget_data'],$_POST['widget_id']]);
            echo "Widget updated";
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
        $dir = "src/";
        $packages = scandir($dir);
        $table = '<tr><th class="gs-2 col-xs-2"><th class="gs-2 col-xs-8"><th class="gs-2 col-xs-2">';
        $pn = 0; $alert = '';

        $activate = router::get('activate');
        if (array_search($activate,$packages)) {
            if(($key = array_search($activate, $GLOBALS['config']['packages'])) === false) {
                $GLOBALS['config']['packages'][] = $activate;
                gila::updateConfigFile();
                $alert = gila::alert('success','Package activated');
            }
        }

        $deactivate = router::get('deactivate');
        if (array_search($deactivate,$packages)) {
            if(($key = array_search($deactivate, $GLOBALS['config']['packages'])) !== false) {
                unset($GLOBALS['config']['packages'][$key]);
                gila::updateConfigFile();
                $alert = gila::alert('success','Package deactivated');
            }
        }

        include 'src/core/views/admin/header.php';
        echo $alert;

        foreach ($packages as $p) if($p[0] != '.') if(file_exists($dir."$p/package.php")){
            include $dir."$p/package.php";

            if (file_exists($dir."$p/logo.png")) {
                $table .= '<tr><td><div><img src="'."src/$p/logo.png".'" style="width:100%" /></div>';
            }
            else {
                $table .= '<tr><td style="background:#999; align:middle"><span>'.($name?:$p).'</span>';
            }

            $table .= '<td><h4>'.($name?:$p).' '.($version?:'').'</h4>'.($description?:'No description');
            $table .= '<td>';

            if (in_array($p,$GLOBALS['config']['packages'])) {
                //if (new_version) $table .= 'Upgrade<br>';
                $table .= "<a href='admin/addons?deactivate={$p}' class='btn error'>Deactivate</a>";
            }
            else {
                $table .= "<a href='admin/addons?activate={$p}' class='btn success'>Activate</a>";
            }
            $pn++;
        }
        //echo "<span>$pn packages found</span>";
        echo "<table class='table'>$table</table>";
        include 'src/core/views/admin/footer.php';
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

}
