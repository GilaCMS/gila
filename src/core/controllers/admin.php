<?php

use Gila\User;
use Gila\Config;
use Gila\View;
use Gila\Session;
use Gila\Router;
use Gila\Package;
use Gila\Widget;

class admin extends Gila\Controller
{
  public function __construct()
  {
    self::admin();
    Config::addLang('core/lang/admin/');
  }

  /**
  * Renders admin/dashboard.php
  */
  public function indexAction()
  {
    $id = Router::path() ?? null;

    if ($id && ($r = Gila\Page::getByIdSlug($id)) && ($r['publish']==1)
        && ($id!='' && Router::controller()=='admin')) {
      View::set('title', $r['title']);
      View::set('text', $r['page']);
      if ($r['template']==''||$r['template']===null) {
        View::renderFile('page--admin.php');
      } else {
        View::renderFile('page--'.$r['template'].'.php');
      }
      return;
    }

    if (Router::get('action', 1)) {
      http_response_code(404);
      View::renderAdmin('404.php');
      return;
    }
    $this->dashboardAction();
  }

  public function dashboardAction()
  {
    global $db;
    $wfolders=['log','themes','src','tmp','assets','data'];
    foreach ($wfolders as $wf) {
      if (is_writable($wf)==false) {
        View::alert('warning', $wf.' folder is not writable. Permissions may have to be adjusted.');
      }
    }
    if (Session::hasPrivilege('admin') && FS_ACCESS && Package::check4updates()) {
      View::alert('warning', '<a class="g-btn" href="?c=admin&action=packages">'.__('_updates_available').'</a>');
    }

    View::renderAdmin('admin/dashboard.php');
  }

  /**
  * List and edit widgets
  */
  public function widgetsAction()
  {
    if ($id = Router::get('id', 1)) {
      View::set('widget', Widget::getById($id));
      View::renderFile('admin/edit_widget.php');
      return;
    }
  }

  public function contentAction($type = null, $id = null)
  {
    if ($type == null) {
      if (Session::hasPrivilege('admin')) {
        View::renderAdmin('admin/contenttype.php');
      } else {
        http_response_code(404);
        View::renderAdmin('404.php');
      }
      return;
    }

    $src = explode('.', Config::$content[$type])[0];
    View::set('table', $type);
    View::set('tablesrc', $src);
    if ($id == null) {
      View::renderAdmin('admin/content-vue.php');
    } else {
      View::set('id', $id);
      View::renderAdmin('admin/content-edit.php');
    }
  }

  public function update_widgetAction()
  {
    echo Widget::update($_POST);
  }

  public function usersAction()
  {
    View::renderAdmin('admin/users.php');
  }

  public function package_optionsAction()
  {
    self::access('admin');
    $package = Router::get('package', 1);
    View::renderFile('admin/header.php');
    Package::options($package);
    View::renderFile('admin/footer.php');
  }

  /**
  * List and manage installed packages
  * @photo
  */
  public function packagesAction()
  {
    self::access('admin');
    if ($_SERVER['REQUEST_METHOD']=='POST' || isset($_GET['test'])) {
      new Package();
      return;
    }
    $search = htmlentities(Router::get('search', 2));
    $tab = Router::get('tab', 1);
    $packages = [];

    if ($tab == 'new') {
      $url = 'https://gilacms.com/packages/?search='.$search;
      $url .= Config::config('test')=='1' ? '&test=1' : '';
      if (!$contents = file_get_contents($url)) {
        View::alert('error', "Could not connect to packages list. Please try later.");
      } else {
        $packages = json_decode($contents);
      }
    } else {
      $packages = Package::scan();
    }
    if (!is_array($packages)) {
      View::alert('error', "Something went wrong. Please try later.");
      $packages = [];
    }
    View::set('packages', $packages);
    View::set('search', $search);
    View::renderAdmin('admin/package-list.php');
  }

  public function newthemesAction()
  {
    self::access('admin');
    $packages = [];
    $search = htmlentities(Router::get('search', 2));
    if (!$contents = file_get_contents('https://gilacms.com/packages/themes?search='.$search)) {
      View::alert('error', "Could not connect to themes list. Please try later.");
    } else {
      $packages = json_decode($contents);
    }
    if (!is_array($packages)) {
      View::alert('error', "Something went wrong. Please try later.");
      $packages = [];
    }
    View::set('packages', $packages);
    View::set('search', $search);
    View::renderAdmin('admin/theme-list.php');
  }

  public function themesAction()
  {
    self::access('admin');
    new Gila\Theme();
    $packages = Gila\Theme::scan();
    View::set('packages', $packages);
    View::renderAdmin('admin/theme-list.php');
  }

  public function theme_optionsAction()
  {
    View::renderAdmin('admin/theme-options.php');
  }

  public function settingsAction()
  {
    self::access('admin');
    View::renderAdmin('admin/settings.php');
  }

  public function loginAction()
  {
    View::set('title', __('Log In'));
    View::renderAdmin('login.php');
  }

  public function logoutAction()
  {
    global $db;
    User::metaDelete(Session::userId(), 'GSESSIONID', $_COOKIE['GSESSIONID']);
    Session::destroy();
    echo "<meta http-equiv='refresh' content='0;url=".Config::config('base')."' />";
  }

  public function media_uploadAction()
  {
    if (isset($_FILES['uploadfiles'])) {
      if (isset($_FILES['uploadfiles']["error"])) {
        if ($_FILES['uploadfiles']["error"] > 0) {
          echo "Error: " . $_FILES['uploadfiles']['error'] . "<br>";
        }
      }
      $upload_folder = Config::config('media_uploads') ?? 'assets';
      $path = Router::post('path', $upload_folder);
      if ($path[0]=='.') {
        $path=$upload_folder;
      }
      $tmp_file = $_FILES['uploadfiles']['tmp_name'];
      $name = htmlentities($_FILES['uploadfiles']['name']);
      if (in_array(pathinfo($name, PATHINFO_EXTENSION), ["jpg","JPG","jpeg","JPEG","png","PNG","gif","GIF"])) {
        $path = SITE_PATH.$path.'/'.$name;
        if (!move_uploaded_file($tmp_file, $path)) {
          echo "Error: could not upload file!<br>";
        }
        $maxWidth = Config::config('maxImgWidth') ?? 0;
        $maxHeight = Config::config('maxImgHeight') ?? 0;
        if ($maxWidth>0 && $maxHeight>0) {
          Image::makeThumb($path, $path, $maxWidth, $maxHeight);
        }
      } else {
        echo "<div class='alert error'>Error: not a media file!</div>";
      }
    }

    self::mediaAction();
  }

  public function mediaAction()
  {
    View::renderAdmin('admin/media.php');
  }


  public function fmAction()
  {
    self::access('admin');
    if (FS_ACCESS) {
      $file=realpath(htmlentities($_GET['f']));
      View::set('filepath', $file);
      View::renderAdmin('admin/fm-index.php');
    } else {
      http_response_code(404);
      View::renderAdmin('404.php');
    }
  }


  public function profileAction()
  {
    Config::addLang('core/lang/myprofile/');
    $user_id = Session::key('user_id');
    Gila\Profile::postUpdate($user_id);
    View::set('page_title', __('My Profile'));
    View::set('twitter_account', User::meta($user_id, 'twitter_account'));
    View::set('token', User::meta($user_id, 'token'));
    View::set('user_photo', User::meta($user_id, 'photo'));
    View::renderAdmin('admin/myprofile.php');
  }

  public function deviceLogoutAction()
  {
    $device = Router::request('device');
    if (User::logoutFromDevice($device)) {
      $info = [];
      $sessions = User::metaList(Session::userId(), 'GSESSIONID');
      foreach ($sessions as $key=>$session) {
        $user_agent = json_decode(file_get_contents(LOG_PATH.'/sessions/'.$session))->user_agent;
        $info[$key] = UserAgent::info($user_agent);
        if ($_COOKIE['GSESSIONID']==$session) {
          $info[$key]['current'] = true;
        }
      }
      echo json_encode($info);
    } else {
      echo json_encode([
        'error'=>'Could not log you out from this device'
      ]);
    }
  }

  public function phpinfoAction()
  {
    self::access('admin');
    if (!FS_ACCESS) {
      return;
    }
    View::includeFile('admin/header.php');
    phpinfo();
    View::includeFile('admin/footer.php');
  }

  public function menuAction()
  {
    $menu = Router::get('menu', 1);
    if ($menu != null) {
      if (Session::hasPrivilege('admin')) {
        $folder = Config::dir(LOG_PATH.'/menus/');
        $file = $folder.$menu.'.json';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
          if (isset($_POST['menu'])) {
            file_put_contents($file, strip_tags($_POST['menu']));
            echo json_encode(["msg"=>__('_changes_updated')]);
            exit;
          }
        } elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
          unlink($file);
          echo json_encode(["msg"=>__('_changes_updated')]);
          exit;
        }
      }
    }
    View::set('menu', ($menu?:'mainmenu'));
    View::renderAdmin('admin/menu_editor.php');
  }

  public function speedAction()
  {
    timeDebug('here');
  }
}
