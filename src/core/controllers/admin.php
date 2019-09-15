<?php

use core\models\widget;
use core\models\user;

class admin extends controller
{

  public function __construct ()
  {
    self::admin();
    gila::addLang('core/lang/admin/');
  }

  /**
  * Renders admin/dashboard.php
  */
  function indexAction ()
  {
    global $db;
    if(router::get('action', 1)) {
      http_response_code(404);
      view::renderAdmin('404.php');
      return;
    }
    $wfolders=['log','themes','src','tmp','assets'];
    foreach($wfolders as $wf) if(is_writable($wf)==false) {
      view::alert('warning', $wf.' folder is not writable. Permissions may have to be adjusted.');
    }
    if(package::check4updates()) {
      view::alert('warning','<a class="g-btn" href="?c=admin&action=packages">'.__('_updates_available').'</a>');
    }
    $db->connect();
    view::set('posts',$db->value('SELECT count(*) from post;'));
    view::set('pages',$db->value('SELECT count(*) from page;'));
    view::set('users',$db->value('SELECT count(*) from user;'));
    $db->close();
    view::set('packages',count($GLOBALS['config']['packages']));
    view::renderAdmin('admin/dashboard.php');
  }

  /**
  * List and edit widgets
  */
  function widgetsAction ()
  {
    if ($id = router::get('id',1)) {
      view::set('widget',widget::getById($id));
      view::renderFile('admin/edit_widget.php');
      return;
    }
  }

  function contentAction($type = null)
  {
    if($type != null) {
      if(!isset(gila::$content[$type])) {
        http_response_code(404);
        view::renderAdmin('404.php');
        return;
      }
      $src = explode('.',gila::$content[$type])[0];
      view::set('table', $type);
      view::set('tablesrc', $src);
      view::renderAdmin('admin/content-vue.php');
    } else if(gila::hasPrivilege('admin')) view::renderAdmin('admin/contenttype.php');
  }

  function update_widgetAction ()
  {
    echo widget::update($_POST);
  }

  function usersAction ()
  {
    view::renderAdmin('admin/users.php');
  }

  function package_optionsAction()
  {
    $package = router::get('package',1);
    view::renderFile('admin/header.php');
    package::options($package);
    view::renderFile('admin/footer.php');
  }

  /**
  * List and manage installed packages
  * @photo
  */
  function packagesAction ()
  {
    new package();
    $search = htmlentities(router::get('search',2));
    $tab = router::get('tab',1);
    $packages = [];

    if($tab == 'new') {
      if(!$contents = file_get_contents('https://gilacms.com/packages/?search='.$search)) {
          view::alert('error',"Could not connect to packages list. Please try later.");
      } else $packages = json_decode($contents);
    } else {
      $packages = package::scan();
    }
    if(!is_array($packages)) {
      view::alert('error',"Something went wrong. Please try later.");
      $packages = [];
    }
    view::set('packages',$packages);
    view::set('search',$search);
    view::renderAdmin('admin/package-list.php');
  }

  function newthemesAction ()
  {
    $packages = [];
    $search = htmlentities(router::get('search',2));
    //if(!$contents = file_get_contents('https://gilacms.com/cm/list/?t=theme&search='.$search)) {
    if(!$contents = file_get_contents('https://gilacms.com/packages/themes?search='.$search)) {
        view::alert('error',"Could not connect to themes list. Please try later.");
    } else {
      $packages = json_decode($contents);
    }
    if(!is_array($packages)) {
      view::alert('error',"Something went wrong. Please try later.");
      $packages = [];
    }
    view::set('packages',$packages);
    view::set('search',$search);
    view::renderAdmin('admin/theme-list.php');
  }

  function themesAction ()
  {
    new theme();
    $packages = theme::scan();
    view::set('packages',$packages);
    view::renderAdmin('admin/theme-list.php');
  }

  function theme_optionsAction ()
  {
    view::renderAdmin('admin/theme-options.php');
  }

  function settingsAction ()
  {
    view::renderAdmin('admin/settings.php');
  }

  function loginAction ()
  {
    view::renderAdmin('login.php');
  }

  function logoutAction ()
  {
    global $db;
    $res = $db->query("DELETE FROM usermeta WHERE user_id=? AND vartype='GSESSIONID';",[session::key('user_id')]);
    session::destroy();
    echo "<meta http-equiv='refresh' content='0;url=".gila::config('base')."' />";
  }

  function media_uploadAction(){
    if(!gForm::posted()) {
      echo "Permission denied.";
      exit;
    }
    if(isset($_FILES['uploadfiles'])) {
      if (isset($_FILES['uploadfiles']["error"])) if ($_FILES['uploadfiles']["error"] > 0) {
        echo "Error: " . $_FILES['uploadfiles']['error'] . "<br>";
      }
      $path = router::post('path','assets');
      if($path[0]=='.') $path='assets';
      $tmp_file = $_FILES['uploadfiles']['tmp_name'];
      $name = htmlentities($_FILES['uploadfiles']['name']);
      if(in_array(pathinfo($name, PATHINFO_EXTENSION),["svg","jpg","JPG","jpeg","JPEG","png","PNG","gif","GIF"])) {
        if(!move_uploaded_file($tmp_file, SITE_PATH.$path.'/'.$name)) {
          echo "Error: could not upload file!<br>";
        }
      } else echo "<div class='alert error'>Error: not a media file!</div>";
    }

    self::mediaAction();
  }

  function mediaAction()
  {
    view::renderAdmin('admin/media.php');
    event::fire('admin::media');
  }

  function db_backupAction()
  {
    new db_backup();
  }

  function fmAction()
  {
    if(FS_ACCESS) {
      $file=realpath(htmlentities($_GET['f']));
      view::set('filepath',$file);
      view::renderAdmin('admin/fm-index.php');
    } else {
      http_response_code(404);
      view::renderAdmin('404.php');
    }
  }

  function sqlAction()
  {
    if(gila::hasPrivilege('admin')==false) return;
    if($q=router::request('query')) view::set('q', $q);
    view::renderAdmin('admin/sql.php');
  }
  
  function profileAction()
  {
    gila::addLang('core/lang/myprofile/');
    $user_id = session::key('user_id');
    core\models\profile::postUpdate($user_id);
    view::set('page_title', __('My Profile'));
    view::set('twitter_account',user::meta($user_id,'twitter_account'));
    view::renderAdmin('admin/myprofile.php');
  }

  function phpinfoAction()
  {
    if(!FS_ACCESS) return;
    view::includeFile('admin/header.php');
    phpinfo();
    view::includeFile('admin/footer.php');
  }

  function menuAction()
  {
    $menu = router::get('menu',1);
    if($menu != null) if(gila::hasPrivilege('admin')) {
      $folder = gila::dir(LOG_PATH.'/menus/');
      $file = $folder.$menu.'.json';
      if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(isset($_POST['menu'])) {
          file_put_contents($file,strip_tags($_POST['menu']));
          echo json_encode(["msg"=>__('_changes_updated')]);
          exit;
        }
      } else if($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        unlink($file);
        echo json_encode(["msg"=>__('_changes_updated')]);
        exit;
      }
    }
    view::set('menu',($menu?:'mainmenu'));
    view::renderAdmin('admin/menu_editor.php');
  }

}
