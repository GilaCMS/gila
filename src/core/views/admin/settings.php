<?php
$config_list = ['title'=>'Title', 'slogan'=>'Description', 'base'=>'Website URL', 'admin_email'=>'Admin Email'];
foreach ($_POST as $key=>$value) {
  $_POST[$key] = strip_tags($value);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $_lc=substr($_POST['gila_base'], -1);
  if ($_lc!='/' && $_lc!='\\') {
    $_POST['gila_base'].='/';
  }
  foreach ($config_list as $key => $value) {
    Config::setConfig($key, $_POST['gila_'.$key]);
  }
  Config::config('default-controller', $_POST['gila_dc']);
  Config::config('timezone', $_POST['gila_timezone']);
  Config::config('language', $_POST['gila_language']);
  Config::config('admin_logo', $_POST['gila_admin_logo']);
  Config::config('admin_theme', $_POST['gila_admin_theme']);
  Config::config('favicon', $_POST['gila_favicon']);
  Config::config('env', $_POST['gila_env']);
  Config::config('check4updates', $_POST['gila_check4updates']);
  Config::config('rewrite', $_POST['gila_rewrite']);
  Config::config('user_register', $_POST['gila_user_register']);
  Config::config('user_activation', $_POST['gila_user_activation']);
  Config::config('use_cdn', $_POST['gila_use_cdn']);
  Config::config('use_webp', $_POST['gila_webp']);
  Config::config('maxImgWidth', $_POST['gila_maxImgWidth']);
  Config::config('maxImgHeight', $_POST['gila_maxImgHeight']);
  Config::updateConfigFile();
  echo '{"success":true}';
  return;
}
View::script('core/admin/media.js');
View::script('core/lang/content/'.Config::config('language').'.js');
?>
<style>.g-switch{z-index:1}#settings-form>div>*{width:33%;display:inline-block}</style>

<div class="gm-12">
<?php View::alerts(); ?>
<form id="settings-form" method="post" action="<?=Config::make_url('admin', 'settings')?>" class="g-form">
  <input type="hidden" name="submit-btn">
  <h2><?=__("Basic Settings")?></h2><hr>

<?php
foreach ($config_list as $key=>$value) {
  if ($value[0] != '.') { ?>
  <br><div class="gm-12">
  <label class="g-label gm-4"><?=__($value)?></label>
  <input class="g-input" name="gila_<?=$key?>" value="<?=Config::config($key)?>" class="gm-4" />
  </div>
<?php }
} ?>

  <br>
  <?php echo Form::input('gila_user_register', ["type"=>"switch"], Config::config('user_register'), __("New users can register")) ?>

  <br>
  <?php echo Form::input('gila_user_activation', ["type"=>"select","options"=>['auto'=>__('Automatically'),'byemail'=>__('Email activation link'),'byadmin'=>__('Administration')]], Config::config('user_activation'), __("New Users activation")) ?>

  <br><div class="gm-12">
    <label class="g-label gm-4"><?=__("Timezone")?></label>
    <select name="gila_timezone" value="<?=Config::config('timezone')?>" class="gm-4">
    <?php
    foreach (DateTimeZone::listIdentifiers() as $value) {
      $sel = (Config::config('timezone')==$value?'selected':'');
      echo '<option value="'.$value."\" $sel>".ucwords($value).'</option>';
    }
    ?>
    </select>
  </div>

  <br><div class="gm-12">
    <label class="g-label gm-4"><?=__("Language")?></label>
    <select name="gila_language" value="<?=Config::config('language')?>" class="gm-4">
    <?php
    $languages = include 'src/core/lang/languages.php';
    foreach ($languages as $k=>$value) {
      $sel = (Config::config('language')==$k?'selected':'');
      echo '<option value="'.$k."\" $sel>".ucwords($value).'</option>';
    }
    ?>
    </select>
  </div>

  <br><div class="gm-12">
    <label class="g-label gm-4"><?=__("Admin Logo")?></label>
    <div class="gm-4" style="display:inline-flex"><span class="g-group">
      <span class="btn g-group-item" onclick="open_media_gallery('#m_admin_logo')"><i class="fa fa-image"></i></span>
      <span class="g-group-item"><input class="fullwidth g-input" value="<?=Config::config('admin_logo')?>" id="m_admin_logo" name="gila_admin_logo"></span>
    </span></div>
  </div>

  <br><div class="gm-12">
    <label class="g-label gm-4"><?=__("Favicon")?></label>
    <div class="gm-4" style="display:inline-flex"><span class="g-group">
      <span class="btn g-group-item" onclick="open_media_gallery('#m_favicon')"><i class="fa fa-image"></i></span>
      <span class="g-group-item"><input class="fullwidth g-input" value="<?=Config::config('favicon')?>" id="m_favicon" name="gila_favicon"></span>
    </span></div>
  </div>

  <br>
  <?php
  $options = ['default'=>'Default', 'deepblue'=>'Deep Blue', 'liquidcool'=>'Liquid Cool', ''=>'Old'];
  echo Form::input('gila_admin_theme', ["type"=>"select","options"=>$options], Config::config('admin_theme'), __("Admin Theme"));
  ?>

  <br>
  <a class="g-btn" onclick="save_settings()"><?=__("Submit")?></a>

  <h2><?=__("Advanced Settings")?></h2><hr>

  <br>
  <?php echo Form::input('gila_use_cdn', ["type"=>"switch"], Config::config('use_cdn'), __("Use CDN")) ?>

  <br>
  <?php echo Form::input('gila_rewrite', ["type"=>"switch"], Config::config('rewrite'), __("Pretty Urls")) ?>

  <br><div class="gm-12">
  <label class="g-label gm-4"><?=__("Default Controller")?></label>
  <select name="gila_dc" value="<?=Config::config('default-controller')?>" class="gm-4">
  <?php
  foreach (Router::$controllers as $k=>$value) {
    if ($value[0] != '.') {
      if (!in_array($k, ['cm','login','webhook','fm','lzld','blocks'])) {
        $sel = (Config::config('default-controller')==$k?'selected':'');
        echo '<option value="'.$k."\" $sel>".ucwords($k).'</option>';
      }
    }
  }
  ?>
  </select>
  </div>

  <br>
  <?php echo Form::input('gila_env', ["type"=>"select","options"=>['pro'=>__('Production'),'dev'=>__('Development')]], Config::config('env'), __("Environment")) ?>

  <br>
  <?php echo Form::input('gila_check4updates', ["type"=>"switch"], Config::config('check4updates'), __("Check For Updates")) ?>

  <br>
  <?php echo Form::input('gila_webp', ["type"=>"switch"], Config::config('use_webp'), __("Use WEBP")) ?>

  <br>
  <div class="gm-12">
  <label class="g-label gm-4"><?=__("Max Media Upload")?> (px)</label>
  <input name="gila_maxImgWidth" value="<?=Config::config('maxImgWidth')?>" type="number" class="gm-2" style="width:120px"/>
  &times;
  <input name="gila_maxImgHeight" value="<?=Config::config('maxImgHeight')?>" type="number" class="gm-2" style="width:120px" />
  </div>

  <br>
  <a class="g-btn" onclick="save_settings()"><?=__("Submit")?></a>
</form>
</div>

<script>
function save_settings() {
  g.postForm('settings-form', function() {
    g.alert('<?=__('_changes_updated')?>', 'success')
  })
}
</script>
