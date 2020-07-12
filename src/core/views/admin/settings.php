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
    Gila\Gila::setConfig($key, $_POST['gila_'.$key]);
  }
  Gila\Gila::config('default-controller', $_POST['gila_dc']);
  Gila\Gila::config('timezone', $_POST['gila_timezone']);
  Gila\Gila::config('language', $_POST['gila_language']);
  Gila\Gila::config('admin_logo', $_POST['gila_admin_logo']);
  Gila\Gila::config('favicon', $_POST['gila_favicon']);
  Gila\Gila::config('env', $_POST['gila_env']);
  Gila\Gila::config('check4updates', $_POST['gila_check4updates']);
  Gila\Gila::config('rewrite', $_POST['gila_rewrite']);
  Gila\Gila::config('user_register', $_POST['gila_user_register']);
  Gila\Gila::config('user_activation', $_POST['gila_user_activation']);
  Gila\Gila::config('use_cdn', $_POST['gila_use_cdn']);
  Gila\Gila::config('use_webp', $_POST['gila_webp']);
  Gila\Gila::config('maxImgWidth', $_POST['gila_maxImgWidth']);
  Gila\Gila::config('maxImgHeight', $_POST['gila_maxImgHeight']);
  Gila\Gila::updateConfigFile();
  echo '{"success":true}';
  return;
}
Gila\View::script('core/admin/media.js');
Gila\View::script('core/lang/content/'.Gila\Gila::config('language').'.js');
?>
<style>.g-switch{z-index:1}#settings-form>div>*{width:33%;display:inline-block}</style>

<div class="gm-12">
<?php Gila\View::alerts(); ?>
<form id="settings-form" method="post" action="<?=Gila\Gila::make_url('admin', 'settings')?>" class="g-form">
  <input type="hidden" name="submit-btn">
  <h2><?=__("Basic Settings")?></h2><hr>

<?php
foreach ($config_list as $key=>$value) {
  if ($value[0] != '.') { ?>
  <br><div class="gm-12">
  <label class="g-label gm-4"><?=__($value)?></label>
  <input class="g-input" name="gila_<?=$key?>" value="<?=Gila\Gila::config($key)?>" class="gm-4" />
  </div>
<?php }
} ?>

  <br>
  <?php echo Gila\Form::input('gila_user_register', ["type"=>"switch"], Gila\Gila::config('user_register'), __("New users can register")) ?>

  <br>
  <?php echo Gila\Form::input('gila_user_activation', ["type"=>"select","options"=>['auto'=>__('Automatically'),'byemail'=>__('Email activation link'),'byadmin'=>__('Administration')]], Gila\Gila::config('user_activation'), __("New Users activation")) ?>

  <br><div class="gm-12">
    <label class="g-label gm-4"><?=__("Timezone")?></label>
    <select name="gila_timezone" value="<?=Gila\Gila::config('timezone')?>" class="gm-4">
    <?php
    foreach (DateTimeZone::listIdentifiers() as $value) {
      $sel = (Gila\Gila::config('timezone')==$value?'selected':'');
      echo '<option value="'.$value."\" $sel>".ucwords($value).'</option>';
    }
    ?>
    </select>
  </div>

  <br><div class="gm-12">
    <label class="g-label gm-4"><?=__("Language")?></label>
    <select name="gila_language" value="<?=Gila\Gila::config('language')?>" class="gm-4">
    <?php
    $languages = include('src/core/lang/languages.php');
    foreach ($languages as $k=>$value) {
      $sel = (Gila\Gila::config('language')==$k?'selected':'');
      echo '<option value="'.$k."\" $sel>".ucwords($value).'</option>';
    }
    ?>
    </select>
  </div>

  <br><div class="gm-12">
    <label class="g-label gm-4"><?=__("Admin Logo")?></label>
    <div class="gm-4" style="display:inline-flex"><span class="g-group">
      <span class="btn g-group-item" onclick="open_media_gallery('#m_admin_logo')"><i class="fa fa-image"></i></span>
      <span class="g-group-item"><input class="fullwidth g-input" value="<?=Gila\Gila::config('admin_logo')?>" id="m_admin_logo" name="gila_admin_logo"></span>
    </span></div>
  </div>

  <br><div class="gm-12">
    <label class="g-label gm-4"><?=__("Favicon")?></label>
    <div class="gm-4" style="display:inline-flex"><span class="g-group">
      <span class="btn g-group-item" onclick="open_media_gallery('#m_favicon')"><i class="fa fa-image"></i></span>
      <span class="g-group-item"><input class="fullwidth g-input" value="<?=Gila\Gila::config('favicon')?>" id="m_favicon" name="gila_favicon"></span>
    </span></div>
  </div>

  <br>
  <a class="g-btn" onclick="save_settings()"><?=__("Submit")?></a>

  <h2><?=__("Advanced Settings")?></h2><hr>

  <br>
  <?php echo Gila\Form::input('gila_use_cdn', ["type"=>"switch"], Gila\Gila::config('use_cdn'), __("Use CDN")) ?>

  <br>
  <?php echo Gila\Form::input('gila_rewrite', ["type"=>"switch"], Gila\Gila::config('rewrite'), __("Pretty Urls")) ?>

  <br><div class="gm-12">
  <label class="g-label gm-4"><?=__("Default Controller")?></label>
  <select name="gila_dc" value="<?=Gila\Gila::config('default-controller')?>" class="gm-4">
  <?php
  foreach (Router::$controllers as $k=>$value) {
    if ($value[0] != '.') {
      if (!in_array($k, ['cm','login','webhook','fm','lzld','blocks'])) {
        $sel = (Gila\Gila::config('default-controller')==$k?'selected':'');
        echo '<option value="'.$k."\" $sel>".ucwords($k).'</option>';
      }
    }
  }
  ?>
  </select>
  </div>

  <br>
  <?php echo Gila\Form::input('gila_env', ["type"=>"select","options"=>['pro'=>__('Production'),'dev'=>__('Development')]], Gila\Gila::config('env'), __("Environment")) ?>

  <br>
  <?php echo Gila\Form::input('gila_check4updates', ["type"=>"switch"], Gila\Gila::config('check4updates'), __("Check For Updates")) ?>

  <br>
  <?php echo Gila\Form::input('gila_webp', ["type"=>"switch"], Gila\Gila::config('use_webp'), __("Use WEBP")) ?>

  <br>
  <div class="gm-12">
  <label class="g-label gm-4"><?=__("Max Media Upload")?> (px)</label>
  <input name="gila_maxImgWidth" value="<?=Gila\Gila::config('maxImgWidth')?>" type="number" class="gm-2" style="width:120px"/>
  &times;
  <input name="gila_maxImgHeight" value="<?=Gila\Gila::config('maxImgHeight')?>" type="number" class="gm-2" style="width:120px" />
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
