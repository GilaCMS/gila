<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
$configfile = CONFIG_PHP;
include_once __DIR__."/../classes/DbClass.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $keys = ['adm_email', 'base_url', 'db_name','db_user'];
  foreach ($keys as $key) {
    if ($_POST[$key]!=strip_tags($_POST[$key])) {
      echo "<div class='alert'><span class='closebtn' onclick='this.parentElement.style.display=\"none\";'>&times;</span>";
      echo "Tags are not allowed in field: ".htmlentities($_POST[$key]);
      include __DIR__."/install.form.php";
      return;
    }
  }

  $host=$_POST['db_host'];
  $db_user=$_POST['db_user'];
  $db_pass=$_POST['db_pass'];
  $db_name=$_POST['db_name'];
  $_base_url=$_POST['base_url'];

  $_lc=substr($_base_url, -1);
  if ($_lc!='/' && $_lc!='\\') {
    $_base_url.='/';
  }

  $link = @mysqli_connect($host, $db_user, $db_pass, $db_name);
  if (!$link) {
    echo "<div class='alert'><span class='closebtn' onclick='this.parentElement.style.display=\"none\";'>&times;</span>";
    echo "#".mysqli_connect_errno().": ".mysqli_connect_error();
    if (mysqli_connect_errno()==2002) {
      echo "<br>(check that database hostname is correct)";
    }
    if (mysqli_connect_errno()==1698) {
      echo "<br>(check that database user is correct)";
    }
    if (mysqli_connect_errno()==1044) {
      echo "<br>(check that database name is correct and user has been granted all the privileges)";
    }
    if (mysqli_connect_errno()==1045) {
      echo "<br>(check that database user's password is correct)";
    }
    echo "</div>";
  } else {
    $db = new Gila\DbClass($host, $db_user, $db_pass, $db_name);
    DB::set($host, $db_user, $db_pass, $db_name);
    include __DIR__."/install.sql.php";
    // create config.php
    $filedata = file_get_contents('config.default.php');
    $GLOBALS['config']['db'] = [
          'host' => $host,
          'user' => $db_user,
          'pass' => $db_pass,
          'name' => $db_name
      ];
    $GLOBALS['config']['permissions'] = [
        1 => [
          0 => 'admin',
          1 => 'admin_user',
          2 => 'admin_userrole'
        ]
      ];
    $GLOBALS['config']['trusted_domains'] = [$_SERVER['HTTP_HOST']];
    $GLOBALS['config']['packages'] = ['blog'];
    $GLOBALS['config']['base'] = $_base_url;
    $GLOBALS['config']['theme'] = 'gila-blog';
    $GLOBALS['config']['title'] = 'Gila CMS';
    $GLOBALS['config']['slogan'] = 'An awesome website!';
    $GLOBALS['config']['default-controller'] = 'blog';
    $GLOBALS['config']['timezone'] = 'America/Mexico_City';
    $GLOBALS['config']['env'] = 'pro';
    $GLOBALS['config']['check4updates'] = 1;
    $GLOBALS['config']['language'] = 'en';
    $GLOBALS['config']['admin_email'] = $_POST['adm_email'];
    $GLOBALS['config']['media_uploads'] = 'data/uploads';
    $GLOBALS['config']['rewrite'] = 1;
    $GLOBALS['config']['page-blocks'] = true;
    $GLOBALS['config']['use_webp'] = function_exists("imagewebp") ? 1 : 0;
    $GLOBALS['config']['utk_level'] = 10;
    $GLOBALS['config']['admin_theme'] = 'default';
    $GLOBALS['config']['admin_logo'] = 'assets/gila-logo.png';
    $GLOBALS['config']['favicon'] = 'assets/favicon.png';

    $filedata = "<?php\n\n\$GLOBALS['config'] = ".var_export($GLOBALS['config'], true).";";
    file_put_contents($configfile, $filedata);
    Gila\Package::copyAssets('core');
    Gila\Theme::copyAssets('gila-blog');
    Gila\Theme::copyAssets('gila-mag');
    Gila\Config::dir(LOG_PATH.'/stats');
    Gila\Config::dir(LOG_PATH.'/sessions');
    Gila\Config::dir(LOG_PATH.'/cacheItem');
    @unlink(LOG_PATH.'/load.php');
    include __DIR__."/installed.php";
    exit;
  }
}

include __DIR__."/install.form.php";
