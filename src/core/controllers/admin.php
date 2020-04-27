<?php

use core\models\widget;
use core\models\user;

class admin extends controller
{

  public function __construct ()
  {
    self::admin();
    Gila::addLang('core/lang/admin/');
  }

  /**
  * Renders admin/dashboard.php
  */
  function indexAction ()
  {
    global $db;
    
    $id = Router::get('page_id',1) ?? null;

    if ($id && ($r = core\models\page::getByIdSlug($id)) && ($r['publish']==1)
        && ($id!='' && Router::controller()=='admin')) {
      View::set('title',$r['title']);
      View::set('text',$r['page']);
      if($r['template']==''||$r['template']===null) {
        View::renderFile('page--admin.php');
      } else {
        View::renderFile('page--'.$r['template'].'.php');
      }
      return;
    }

    if(Router::get('action', 1)) {
      http_response_code(404);
      View::renderAdmin('404.php');
      return;
    }
    $this->dashboardAction();
  }

  function dashboardAction ()
  {
    global $db;
    $wfolders=['log','themes','src','tmp','assets'];
    foreach($wfolders as $wf) if(is_writable($wf)==false) {
      View::alert('warning', $wf.' folder is not writable. Permissions may have to be adjusted.');
    }
    if(Gila::hasPrivilege('admin') && FS_ACCESS && Package::check4updates()) {
      View::alert('warning','<a class="g-btn" href="?c=admin&action=packages">'.__('_updates_available').'</a>');
    }

    $db->connect();
    View::set('posts',$db->value('SELECT count(*) from post;'));
    View::set('pages',$db->value('SELECT count(*) from page;'));
    View::set('users',$db->value('SELECT count(*) from user;'));
    $db->close();
    View::set('packages',count($GLOBALS['config']['packages']));
    View::renderAdmin('admin/dashboard.php');
  }

  /**
  * List and edit widgets
  */
  function widgetsAction ()
  {
    if ($id = Router::get('id',1)) {
      View::set('widget',widget::getById($id));
      View::renderFile('admin/edit_widget.php');
      return;
    }
  }

  function contentAction ($type = null, $id = null)
  {
    if($type == null) {
      if(Gila::hasPrivilege('admin')) {
        View::renderAdmin('admin/contenttype.php');
      } else {
        http_response_code(404);
        View::renderAdmin('404.php');
      }
      return;
    }

    $src = explode('.',Gila::$content[$type])[0];
    View::set('table', $type);
    View::set('tablesrc', $src);
    if($id == null) {
      View::renderAdmin('admin/content-vue.php');
    } else {
      View::set('id', $id);
      View::renderAdmin('admin/content-edit.php');
    }
  }

  function update_widgetAction ()
  {
    echo widget::update($_POST);
  }

  function usersAction ()
  {
    View::renderAdmin('admin/users.php');
  }

  function package_optionsAction()
  {
    self::access('admin');
    $package = Router::get('package',1);
    View::renderFile('admin/header.php');
    Package::options($package);
    View::renderFile('admin/footer.php');
  }

  /**
  * List and manage installed packages
  * @photo
  */
  function packagesAction ()
  {
    self::access('admin');
    if ($_SERVER['REQUEST_METHOD']=='POST' || isset($_GET['test'])) {
      new Package();
      return;
    }
    $search = htmlentities(Router::get('search',2));
    $tab = Router::get('tab',1);
    $packages = [];

    if($tab == 'new') {
      $url = 'https://gilacms.com/packages/?search='.$search;
      $url .= Gila::config('test')=='1' ? '&test=1' : '';
      if(!$contents = file_get_contents($url)) {
          View::alert('error',"Could not connect to packages list. Please try later.");
      } else $packages = json_decode($contents);
    } else {
      $packages = Package::scan();
    }
    if(!is_array($packages)) {
      View::alert('error',"Something went wrong. Please try later.");
      $packages = [];
    }
    View::set('packages',$packages);
    View::set('search',$search);
    View::renderAdmin('admin/package-list.php');
  }

  function newthemesAction ()
  {
    self::access('admin');
    $packages = [];
    $search = htmlentities(Router::get('search',2));
    if(!$contents = file_get_contents('https://gilacms.com/packages/themes?search='.$search)) {
        View::alert('error',"Could not connect to themes list. Please try later.");
    } else {
      $packages = json_decode($contents);
    }
    if(!is_array($packages)) {
      View::alert('error',"Something went wrong. Please try later.");
      $packages = [];
    }
    View::set('packages',$packages);
    View::set('search',$search);
    View::renderAdmin('admin/theme-list.php');
  }

  function themesAction ()
  {
    self::access('admin');
    new theme();
    $packages = theme::scan();
    View::set('packages',$packages);
    View::renderAdmin('admin/theme-list.php');
  }

  function theme_optionsAction ()
  {
    View::renderAdmin('admin/theme-options.php');
  }

  function settingsAction ()
  {
    self::access('admin');
    View::renderAdmin('admin/settings.php');
  }

  function loginAction ()
  {
    View::renderAdmin('login.php');
  }

  function logoutAction ()
  {
    global $db;
    user::metaDelete(Session::user_id(), 'GSESSIONID', $_COOKIE['GSESSIONID']);
    Session::destroy();
    echo "<meta http-equiv='refresh' content='0;url=".Gila::config('base')."' />";
  }

  function media_uploadAction(){
    if(isset($_FILES['uploadfiles'])) {
      if (isset($_FILES['uploadfiles']["error"])) if ($_FILES['uploadfiles']["error"] > 0) {
        echo "Error: " . $_FILES['uploadfiles']['error'] . "<br>";
      }
      $path = Router::post('path','assets');
      if($path[0]=='.') $path='assets';
      $tmp_file = $_FILES['uploadfiles']['tmp_name'];
      $name = htmlentities($_FILES['uploadfiles']['name']);
      if(in_array(pathinfo($name, PATHINFO_EXTENSION),["jpg","JPG","jpeg","JPEG","png","PNG","gif","GIF"])) {
        $path = SITE_PATH.$path.'/'.$name;
        if(!move_uploaded_file($tmp_file, $path)) {
          echo "Error: could not upload file!<br>";
        }
        $maxWidth = Gila::config('maxImgWidth') ?? 0;
        $maxHeight = Gila::config('maxImgHeight') ?? 0;
        if($maxWidth>0 && $maxHeight>0) {
          image::make_thumb($path, $path, $maxWidth, $maxHeight);
        }
      } else echo "<div class='alert error'>Error: not a media file!</div>";
    }

    self::mediaAction();
  }

  function mediaAction()
  {
    View::renderAdmin('admin/media.php');
    Event::fire('admin::media');
  }

  function db_backupAction()
  {
    new db_backup();
  }

  function fmAction()
  {
    self::access('admin');
    if(FS_ACCESS) {
      $file=realpath(htmlentities($_GET['f']));
      View::set('filepath',$file);
      View::renderAdmin('admin/fm-index.php');
    } else {
      http_response_code(404);
      View::renderAdmin('404.php');
    }
  }

  function sqlAction()
  {
    self::access('admin');
    if($q=$_POST['query']) View::set('q', $q);
    View::renderAdmin('admin/sql.php');
  }

  function profileAction()
  {
    Gila::addLang('core/lang/myprofile/');
    $user_id = Session::key('user_id');
    core\models\profile::postUpdate($user_id);
    View::set('page_title', __('My Profile'));
    View::set('twitter_account',user::meta($user_id,'twitter_account'));
    View::set('token',user::meta($user_id,'token'));
    View::renderAdmin('admin/myprofile.php');
  }

  function deviceLogoutAction() {
    $device = Router::request('device');
    if(user::logoutFromDevice($device)) {
      $info = [];
      $sessions = user::metaList(Session::user_id(), 'GSESSIONID');
      foreach($sessions as $key=>$session) {
        $user_agent = json_decode(file_get_contents(LOG_PATH.'/sessions/'.$session))->user_agent;
        $info[$key] = UserAgent::info($user_agent);
        if($_COOKIE['GSESSIONID']==$session) $info[$key]['current'] = true;
      }
      echo json_encode($info);
    } else {
      echo json_encode([
        'error'=>'Could not log you out from this device'
      ]);
    }
  }

  function phpinfoAction()
  {
    self::access('admin');
    if(!FS_ACCESS) return;
    View::includeFile('admin/header.php');
    phpinfo();
    View::includeFile('admin/footer.php');
  }

  function menuAction()
  {
    $menu = Router::get('menu',1);
    if($menu != null) if(Gila::hasPrivilege('admin')) {
      $folder = Gila::dir(LOG_PATH.'/menus/');
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
    View::set('menu',($menu?:'mainmenu'));
    View::renderAdmin('admin/menu_editor.php');
  }

}
