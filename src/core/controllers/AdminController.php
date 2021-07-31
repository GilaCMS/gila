<?php

use Gila\User;
use Gila\Config;
use Gila\View;
use Gila\Session;
use Gila\Router;
use Gila\Package;
use Gila\Widget;

class AdminController extends Gila\Controller
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
    $id = Router::getPath() ?? null;

    if ($r = Gila\Page::getByIdSlug($id)) {
      View::set('title', $r['title']);
      View::set('text', $r['page']);
      if (!empty($r['language'])) {
        Config::lang($r['language']);
      }
      Config::canonical($r['slug']);
      if ($r['template']==''||$r['template']===null) {
        View::renderFile('page.php');
      } else {
        View::renderFile('page--'.$r['template'].'.php');
      }
      return;
    }

    if (Router::param('action', 1)) {
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
    if (!file_exists('assets/lib/tinymce5')) {
      View::alert('alert', 'You have to download tinymce5 and add it to assets/lib/tinymce5 <a href="https://gilacms.com/gila-cms-1-16-0-release>See more</a>');
    }
    if (Session::hasPrivilege('admin') && FS_ACCESS && Package::check4updates()) {
      View::alert('warning', '<a class="g-btn" href="admin/packages">'.__('_updates_available').'</a>');
    }

    View::renderAdmin('admin/dashboard.php');
  }

  /**
  * List and edit widgets
  */
  public function widgetsAction()
  {
    if ($id = Router::param('id', 1)) {
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
    $package = Router::param('package', 1);
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
    $search = htmlentities(Router::param('search', 2));
    $tab = Router::param('tab', 1);
    $packages = [];

    if ($tab == 'new' && FS_ACCESS) {
      $url = 'https://gilacms.com/packages/?search='.$search;
      $url .= Config::get('test')=='1' ? '&test=1' : '';
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
    if (!FS_ACCESS) {
      return;
    }
    $packages = [];
    $search = htmlentities(Router::param('search', 2));
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
    if (Session::userId()>0) {
      header('Location: '.Config::get('base').'admin');
      return;
    }
    View::set('title', __('Log In'));
    View::renderAdmin('login.php');
  }

  public function logoutAction()
  {
    Session::destroy();
    echo "<meta http-equiv='refresh' content='0;url=".Config::get('base')."' />";
  }

  public function media_uploadAction()
  {
    if (isset($_FILES['uploadfiles'])) {
      $upload_folder = Config::get('media_uploads') ?? 'assets';
      $uploadpath = Router::post('path', $upload_folder);
      $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'ogg', 'mkv', 'mp4', 'webm'];
      if (Config::get('allow_svg')) {
        $extensions[] = 'svg';
      }
      if ($uploadpath[0]=='.') {
        $uploadpath = $upload_folder;
      }

      $names = $_FILES['uploadfiles']['name'];
      $tmp_names = $_FILES['uploadfiles']['tmp_name'];
      $errors = $_FILES['uploadfiles']['error'];
      FileManager::$sitepath = realpath(SITE_PATH);

      for ($i=0; $i<count($tmp_names); $i++) {
        $names[$i] = pathinfo($names[$i], PATHINFO_FILENAME).'.'.strtolower(pathinfo($names[$i], PATHINFO_EXTENSION));
        $path = SITE_PATH.$uploadpath.'/'.$names[$i];
        if (isset($errors[$i]) && $errors[$i] > 0) {
          echo "Error: ".$errors[$i]."</div>";
        } elseif (!in_array(strtolower(pathinfo($names[$i], PATHINFO_EXTENSION)), $extensions)) {
          echo "<div class='alert error'>Error: not a media file!</div>";
        } elseif (file_exists($path)) {
          echo "<div class='alert error'>File with same name already exists!</div>";
        } elseif (!FileManager::allowedPath($path)) {
          echo "<div class='alert error'>Error: incorrect path!</div>";
        } elseif (!move_uploaded_file($tmp_names[$i], $path)) {
          echo "<div class='alert error'>Error: could not upload file!</div>";
        } else {
          $maxWidth = Config::get('maxImgWidth') ?? 0;
          $maxHeight = Config::get('maxImgHeight') ?? 0;
          if ($maxWidth>0 && $maxHeight>0) {
            Image::makeThumb($path, $path, $maxWidth, $maxHeight);
          }
        }
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
    View::set('user_id', $user_id);
    View::renderAdmin('admin/myprofile.php');
  }

  public function sessionsAction()
  {
    Config::addLang('core/lang/myprofile/');
    $user_id = Session::key('user_id');
    View::set('page_title', __('Sessions'));
    View::renderAdmin('admin/mysessions.php');
  }

  public function deviceLogoutAction()
  {
    $device = Router::request('device');
    if (User::logoutFromDevice($device)) {
      $info = [];
      $sessions = Session::findByUserId(Session::userId());
      foreach ($sessions as $key=>$session) {
        $user_agent = $session['user_agent'];
        $info[$key] = Gila\UserAgent::info($user_agent);
        $info[$key]['ip'] = $session['ip_address'];
        if ($_COOKIE['GSESSIONID']==$session['gsessionid']) {
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
    if (!FS_ACCESS) {
      http_response_code(404);
      View::renderAdmin('404.php');
      return;
    }
    self::access('admin');
    View::includeFile('admin/header.php');
    phpinfo();
    View::includeFile('admin/footer.php');
  }

  public function menuAction($menu = null)
  {
    if ($menu != null) {
      if (Session::hasPrivilege('admin')) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
          if (isset($_POST['menu'])) {
            Gila\Menu::setContents($menu, strip_tags($_POST['menu']));
            echo json_encode(["msg"=>__('_changes_updated')]);
            exit;
          }
        } elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
          Gila\Menu::remove($menu);
          echo json_encode(["msg"=>__('_changes_updated')]);
          exit;
        }
      }
    }
    View::set('menu', ($menu??'mainmenu'));
    View::renderAdmin('admin/menu_editor.php');
  }

  public function notificationsAction($type = null)
  {
    View::set('type', $type);
    View::renderAdmin('admin/notifications.php');
  }
}
