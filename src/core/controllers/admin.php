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
            view::set('id',$id);
            view::renderAdmin('admin/edit_post.phtml');
            return;
        }
        view::set('page', (router::get('page',1)?:1));
        view::set('rpp', 10);
        view::renderAdmin('admin/list_post.phtml');
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
        $table = '<tr><th class="col-xs-2 gm-2"><th class="col-xs-8 gm-8"><th class="col-xs-2 gm-2">';
        $pn = 0; $alert = '';

        $activate = router::get('activate');
        if (in_array($activate,$packages)) {
            if(!in_array($activate, $GLOBALS['config']['packages'])) {
                $GLOBALS['config']['packages'][]=$activate;
                $response = gila::updateConfigFile();
                $alert = gila::alert('success','Package activated');
                exit;
            }
        }

        $deactivate = router::get('deactivate');
        if (in_array($deactivate,$GLOBALS['config']['packages'])) {
            $key = array_search($deactivate, $GLOBALS['config']['packages']);
                unset($GLOBALS['config']['packages'][$key]);
                $response = gila::updateConfigFile();
                $alert = gila::alert('success',"Package $key deactivated");
                exit;
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
                $table .= "<a onclick='addon_deactivate(\"{$p}\")' class='btn error'>Deactivate</a>";
            }
            else {
                $table .= "<a onclick='addon_activate(\"{$p}\")' class='btn success'>Activate</a>";
            }
            $pn++;
        }
        //echo "<span>$pn packages found</span>";
        echo "<table class='g-table'>$table</table>";
        echo "<script>
        function addon_activate(p){ g.ajax('admin/addons?activate='+p,function(x){
            g.alert('Package successfully activated!','success','location.reload()');
            })};
        function addon_deactivate(p){ g.ajax('admin/addons?deactivate='+p,function(x){
            g.alert('Package deactivated!','notice','location.reload()');
             })};
        </script>";
        include 'src/core/views/admin/footer.php';
        /*setTimeout(function () {
			for(let attr in data){
				template.content.firstChild[attr] = data[attr]
			}
		}, 100)*/
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
