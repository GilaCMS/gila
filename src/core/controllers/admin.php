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
      echo "Dashboard!";
    }

    function postsAdmin ()
    {
        global $db;
        if ($id = router::get('id',1)) {
            $res = $db->query("SELECT * FROM post WHERE id=?",$id);
            while ($r = mysqli_fetch_array($res)) {
                view::set('title',$r['title']);
                view::set('text',$r['post']);
                view::render('views/edit_post.phtml');
            }

            return;
        }
        echo "<table class=\"table\"><tr><th>ID<th>Title<th>Slug<th>User ID<th>Updated<th>";
        $page = router::get('page',1)?:1;
        $rpp = 10;
        $lstart = $page*$rpp-$rpp;
        $limit = "LIMIT $lstart,$rpp";

        $gen = $db->gen("SELECT * FROM post $limit");
        foreach ($gen as $r) {
            echo '<tr>'.'<td>'.$r['id'].'<td>'.$r['title'].'<td>'.$r['slug'].'<td>'.$r['user_id'].'<td>'.$r['updated'].'<td><a href="admin/posts/'.$r['id'].'">Edit</a>';
        }
        echo "</table>";

        $total = $db->value("SELECT COUNT(*) FROM post")?:0;
        echo '<p>';
        for ($i=1; $i<= $total/$rpp+1; $i++) {
            echo " <a class='btn btn-primary' href='".router::url()."?page=$i'>$i</a>";
        }
        echo '</p>';
    }

    function widgetsAdmin ()
    {
        global $db;
        if ($id = router::get('id',1)) {
            $res = $db->query("SELECT * FROM widget WHERE id=?",$id);
            while ($r = mysqli_fetch_array($res)) {
                echo "<h2>Edit widget #".$r['id']."</h2>";
                /*view::set('title',$r['title']);*/
                view::set('widget_id',$r['id']);
                view::render('widgets/'.$r['widget'].'/edit.phtml');
            }

            return;
        }
        echo "<table class=\"table\"><tr><th>ID<th>Widget<th>Area<th>Position<th>";

        $gen = $db->gen("SELECT * FROM widget");
        foreach ($gen as $r) {
            echo '<tr>'.'<td>'.$r['id'].'<td>'.$r['widget'].'<td>'.$r['area'].'<td>'.$r['pos'].'<td><a href="admin/widgets/'.$r['id'].'">Edit</a>';
        }
        echo "</table>";
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
        echo "<table class=\"table\"><tr><th>ID<th>Name<th>Email<th>Pass<th>";
        $page = router::get('page',1)?:1;
        $rpp = 10;
        $lstart = $page*$rpp-$rpp;
        $limit = "LIMIT $lstart,$rpp";

        $gen = $db->gen("SELECT * FROM user $limit");
        foreach ($gen as $r) {
            echo '<tr>'.'<td>'.$r['id'].'<td>'.$r['name'].'<td>'.$r['email'].'<td>'.$r['pass'].'<td><a href="admin/users/'.$r['id'].'">Edit</a>';
        }
        echo "</table>";

        $total = $db->value("SELECT COUNT(*) FROM user")?:0;
        echo '<p>';
        for ($i=1; $i<= $total/$rpp; $i++) {
            echo " <a class='btn btn-primary' href='".router::url()."?page=$i'>$i</a>";
        }
        echo '</p>';
    }



    function addonsAdmin ()
    {
        $dir = "src/";
        $packages = scandir($dir);
        $table = '<tr><th class="col-2"><th class="col-8"><th class="col-2">';
        $pn = 0;

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
                $table .= 'Uninstall';
            }
            else {
                $table .= 'Install';
            }
            $pn++;
        }
        //echo "<span>$pn packages found</span>";
        echo "<table class='table'>$table</table>";
    }

    function settingsAdmin ()
    {
        view::render('views/admin/settings.phtml');
    }
    function loginAdmin ()
    {
        view::render('views/login.phtml');
    }
    function logoutAdmin ()
    {
        session::destroy();

    }

}
