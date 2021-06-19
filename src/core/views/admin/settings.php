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
  Config::set('admin_theme', $_POST['gila_admin_theme']??'');
  Config::set('favicon', $_POST['gila_favicon']);
  Config::set('env', $_POST['gila_env']);
  Config::set('check4updates', $_POST['gila_check4updates']);
  Config::set('user_register', $_POST['gila_user_register']);
  Config::set('user_activation', $_POST['gila_user_activation']);
  Config::set('use_cdn', $_POST['gila_use_cdn']);
  Config::set('use_webp', $_POST['gila_webp']);
  Config::set('maxImgWidth', $_POST['gila_maxImgWidth']);
  Config::set('maxImgHeight', $_POST['gila_maxImgHeight']);
  Config::set('utk_level', $_POST['gila_utk_level']);
  Config::set('locale', $_POST['gila_locale']);
  if (isset($_POST['gila_admin_palette'])) {
    Config::set('admin_palette', $_POST['gila_admin_palette']);
  }
  echo '{"success":true}';
  return;
}
View::script('lib/vue/vue.min.js');
View::script('core/lang/content/'.Config::get('language').'.js');
View::script('core/admin/media.js');
View::script('core/admin/vue-components.js');
?>
<style>.g-switch{z-index:1;vertical-align: middle;}
#settings-form>div>*{min-width:33%;display:inline-block}</style>

<div class="gm-12">
<?php View::alerts(); ?>
<form id="settings-form" method="post" action="<?=Config::base('admin/settings')?>" class="g-form">
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
  <?php echo Form::input('gila_user_activation', ["type"=>"select","options"=>['byemail'=>__('Email activation link'),'byadmin'=>__('Administration')]], Config::get('user_activation'), __("New Users activation")) ?>

  <br><div class="gm-12">
    <label class="g-label gm-4"><?=__("Timezone")?></label><select name="gila_timezone" value="<?=Config::get('timezone')?>" class="gm-4">
    <?php
    foreach (DateTimeZone::listIdentifiers() as $value) {
      $sel = (Config::get('timezone')==$value?'selected':'');
      echo '<option value="'.$value."\" $sel>".ucwords($value).'</option>';
    }
    ?>
    </select>
  </div>

  <br><div class="gm-12">
    <label class="g-label gm-4"><?=__("Language")?></label><select name="gila_language" value="<?=Config::get('language')?>" class="gm-4">
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
    <label class="g-label gm-4"><?=__("Admin Logo")?></label><div class="gm-4" style="display:inline-flex">
    <span class="g-group">
      <span class="btn g-group-item" onclick="open_media_gallery('#m_admin_logo')"><i class="fa fa-image"></i></span>
      <span class="g-group-item"><input class="fullwidth g-input" value="<?=Config::get('admin_logo')?>" id="m_admin_logo" name="gila_admin_logo"></span>
    </span></div>
  </div>

  <br><div class="gm-12">
    <label class="g-label gm-4"><?=__("Favicon")?></label><div class="gm-4" style="display:inline-flex">
    <span class="g-group">
      <span class="btn g-group-item" onclick="open_media_gallery('#m_favicon')"><i class="fa fa-image"></i></span>
      <span class="g-group-item"><input class="fullwidth g-input" value="<?=Config::get('favicon')?>" id="m_favicon" name="gila_favicon"></span>
    </span></div>
  </div>

  <br>
  <?php
  $options = ['default'=>'Default', 'deepblue'=>'Deep Blue', 'liquidcool'=>'Liquid Cool', '-'=>'Old'];
  foreach (Config::getList('admin-themes') as $theme) {
    $options[$theme[0]] = $theme[1];
  }
  echo Form::input('gila_admin_theme', ["type"=>"select","options"=>$options], Config::get('admin_theme'), __("Admin Theme"));
  ?>

  <?php
  if (Config::get('admin_palette') || Config::getList('admin-palettes')) {
    $palettes=Config::getList('admin-palettes');
    echo '<br>';
    echo Form::input('gila_admin_palette', ["type"=>"palette","palettes"=>$palettes], Config::get('admin_palette'), __("Admin Palette"));
  }
  ?>

  <br>
  <div>
    <a class="g-btn" style="min-width:unset" onclick="save_settings()"><?=__("Submit")?></a>
  </div>

  <?php
    if (FS_ACCESS) {
      include __DIR__.'/settings-advanced.php';
    }
  ?>
</form>
</div>

<script>
var settingsApp = new Vue({
  el: '#settings-form'
})

function save_settings() {
  g.postForm('settings-form', function() {
    g.alert('<?=__('_changes_updated')?>', 'success')
  })
}
</script>
