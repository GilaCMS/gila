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
        /*view::set('page', (router::get('page',1)?:1));
        view::set('rpp', 10);
        view::renderAdmin('admin/list_post.phtml');*/
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
        //view::set('page', (router::get('page',1)?:1));
        //view::set('rpp', 10);
        //view::renderAdmin('admin/list_page.phtml');
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
        //include 'src/core/views/admin/header.php';
        //include 'src/core/views/admin/footer.php';
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
        $dir = "src/";
        $packages = scandir($dir);
        $table = '<tr><th class="col-xs-2 gm-2"><th class="col-xs-8 gm-8"><th class="col-xs-2 gm-2">';
        $pn = 0; $alert = '';

        $activate = router::get('activate');
        if (in_array($activate,$packages)) {
            if(!in_array($activate, $GLOBALS['config']['packages'])) {
                $GLOBALS['config']['packages'][]=$activate;
                gila::updateConfigFile();
                usleep(100);
                $alert = gila::alert('success','Package activated');
                exit;
            }
        }

        $deactivate = router::get('deactivate');
        if (in_array($deactivate,$GLOBALS['config']['packages'])) {
            $key = array_search($deactivate, $GLOBALS['config']['packages']);
                unset($GLOBALS['config']['packages'][$key]);
                gila::updateConfigFile();
                usleep(100);
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
            g.alert('Package successfully activated!','success','location.reload(true)');
            })};
        function addon_deactivate(p){ g.ajax('admin/addons?deactivate='+p,function(x){
            g.alert('Package deactivated!','notice','location.reload(true)');
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

    function mediaAction()
    {
        $path = router::post('path','assets');
      $files = scandir($path);
      $disabled = ($path=='assets')?'disabled':'';
      $path_array = explode('/',$path);
      array_splice($path_array,count($path_array)-1);
      $uppath=implode('/',$path_array);
      echo "<div class='g-group fullwidth bordered'><a class='btn-white g-group-item fa' id='fm-goup' data-path='$uppath' $disabled><i class='fa fa-arrow-left'></i></a><span class='g-group'>$path</span></div>";
      echo "<input id='selected-path' type='hidden'>";
      echo "<div class='g-gal wrapper gap-8px' style='max-height:250px;overflow-y:scroll;'>";
      foreach($files as $file) if($file[0]!='.'){
        $exp = explode('.',$file);
        if(count($exp)==1) {
          $type='folder';
        } else {
          $imgx = ['jpg','jpeg','png','gif'];
          if(in_array($exp[count($exp)-1],$imgx)) $type='image'; else $type='file';
        }
        $file=$path.'/'.$file;
        if($type=='image') {
            $img='<img src="'.$file.'">';
        } else $img='<i class="fa fa-4x fa-'.$type.' " ></i>';
        echo '<div data-path="'.$file.'"class="gal-path gal-'.$type.'">'.$img.'<br><span>'.$file.'</span></div>';
      }
      echo "</div>";
    }

}
