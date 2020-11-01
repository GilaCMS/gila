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
    Config::set($key, $_POST['gila_'.$key]);
  }
  Config::set('default-controller', $_POST['gila_dc']);
  Config::set('timezone', $_POST['gila_timezone']);
  Config::set('language', $_POST['gila_language']);
  Config::set('admin_logo', $_POST['gila_admin_logo']);
  Config::set('admin_theme', $_POST['gila_admin_theme']);
  Config::set('favicon', $_POST['gila_favicon']);
  Config::set('env', $_POST['gila_env']);
  Config::set('check4updates', $_POST['gila_check4updates']);
  Config::set('rewrite', $_POST['gila_rewrite']);
  Config::set('user_register', $_POST['gila_user_register']);
  Config::set('user_activation', $_POST['gila_user_activation']);
  Config::set('use_cdn', $_POST['gila_use_cdn']);
  Config::set('use_webp', $_POST['gila_webp']);
  Config::set('maxImgWidth', $_POST['gila_maxImgWidth']);
  Config::set('maxImgHeight', $_POST['gila_maxImgHeight']);
  Config::set('utk_level', $_POST['gila_utk_level']);
  Config::set('locale', $_POST['gila_locale']);
  Config::updateConfigFile();
  echo '{"success":true}';
  return;
}
View::script('core/admin/media.js');
View::script('core/lang/content/'.Config::get('language').'.js');
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
  <input class="g-input" name="gila_<?=$key?>" value="<?=Config::get($key)?>" class="gm-4" />
  </div>
<?php }
} ?>

  <br>
  <?php echo Form::input('gila_user_register', ["type"=>"switch"], Config::get('user_register'), __("New users can register")) ?>

  <br>
  <?php echo Form::input('gila_user_activation', ["type"=>"select","options"=>['auto'=>__('Automatically'),'byemail'=>__('Email activation link'),'byadmin'=>__('Administration')]], Config::get('user_activation'), __("New Users activation")) ?>

  <br><div class="gm-12">
    <label class="g-label gm-4"><?=__("Timezone")?></label>
    <select name="gila_timezone" value="<?=Config::get('timezone')?>" class="gm-4">
    <?php
    foreach (DateTimeZone::listIdentifiers() as $value) {
      $sel = (Config::get('timezone')==$value?'selected':'');
      echo '<option value="'.$value."\" $sel>".ucwords($value).'</option>';
    }
    ?>
    </select>
  </div>

  <br><div class="gm-12">
    <label class="g-label gm-4"><?=__("Language")?></label>
    <select name="gila_language" value="<?=Config::get('language')?>" class="gm-4">
    <?php
    $languages = include 'src/core/lang/languages.php';
    foreach ($languages as $k=>$value) {
      $sel = (Config::get('language')==$k?'selected':'');
      echo '<option value="'.$k."\" $sel>".ucwords($value).'</option>';
    }
    ?>
    </select>
  </div>

  <br><div class="gm-12">
    <label class="g-label gm-4"><?=__("Admin Logo")?></label>
    <div class="gm-4" style="display:inline-flex"><span class="g-group">
      <span class="btn g-group-item" onclick="open_media_gallery('#m_admin_logo')"><i class="fa fa-image"></i></span>
      <span class="g-group-item"><input class="fullwidth g-input" value="<?=Config::get('admin_logo')?>" id="m_admin_logo" name="gila_admin_logo"></span>
    </span></div>
  </div>

  <br><div class="gm-12">
    <label class="g-label gm-4"><?=__("Favicon")?></label>
    <div class="gm-4" style="display:inline-flex"><span class="g-group">
      <span class="btn g-group-item" onclick="open_media_gallery('#m_favicon')"><i class="fa fa-image"></i></span>
      <span class="g-group-item"><input class="fullwidth g-input" value="<?=Config::get('favicon')?>" id="m_favicon" name="gila_favicon"></span>
    </span></div>
  </div>

  <br>
  <?php
  $options = ['default'=>'Default', 'deepblue'=>'Deep Blue', 'liquidcool'=>'Liquid Cool', ''=>'Old'];
  echo Form::input('gila_admin_theme', ["type"=>"select","options"=>$options], Config::get('admin_theme'), __("Admin Theme"));
  ?>

  <br>
  <a class="g-btn" onclick="save_settings()"><?=__("Submit")?></a>

  <h2><?=__("Advanced Settings")?></h2><hr>

  <br>
  <?php echo Form::input('gila_use_cdn', ["type"=>"switch"], Config::get('use_cdn'), __("Use CDN")) ?>

  <br>
  <?php echo Form::input('gila_rewrite', ["type"=>"switch"], Config::get('rewrite'), __("Pretty Urls")) ?>

  <br><div class="gm-12">
  <label class="g-label gm-4"><?=__("Default Controller")?></label>
  <select name="gila_dc" value="<?=Config::get('default-controller')?>" class="gm-4">
  <?php
  foreach (Router::$controllers as $k=>$value) {
    if ($value[0] != '.') {
      if (!in_array($k, ['cm','login','webhook','fm','lzld','blocks'])) {
        $sel = (Config::get('default-controller')==$k?'selected':'');
        echo '<option value="'.$k."\" $sel>".ucwords($k).'</option>';
      }
    }
  }
  ?>
  </select>
  </div>

  <br>
  <?php echo Form::input('gila_env', ["type"=>"select","options"=>['pro'=>__('Production'),'dev'=>__('Development')]], Config::get('env'), __("Environment")) ?>

  <br>
  <?php echo Form::input('gila_check4updates', ["type"=>"switch"], Config::get('check4updates'), __("Check For Updates")) ?>

  <br>
  <?php echo Form::input('gila_webp', ["type"=>"switch"], Config::get('use_webp'), __("Use WEBP")) ?>

  <br>
  <div class="gm-12">
  <label class="g-label gm-4"><?=__("Max Media Upload")?> (px)</label>
  <input name="gila_maxImgWidth" value="<?=Config::get('maxImgWidth')?>" type="number" class="gm-2" style="width:120px"/>
  &times;
  <input name="gila_maxImgHeight" value="<?=Config::get('maxImgHeight')?>" type="number" class="gm-2" style="width:120px" />
  </div>

  <br>
  <?php echo Form::input('gila_utk_level', ["type"=>"select","options"=>[0=>'0',1=>'1',2=>'2',3=>'3',4=>'4',5=>'5',6=>'6',7=>'7',8=>'8',9=>'9',10=>'10']], 10, __("Unique Token Key")) ?>

  <br>
  <?php echo Form::input('gila_locale', ["type"=>"text","placeholder"=>"en_US.UTF-8"], null, __("Locale")) ?>
  
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
