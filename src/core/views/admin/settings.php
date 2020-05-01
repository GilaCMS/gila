<?php
$config_list = ['title'=>'Title', 'slogan'=>'Description', 'base'=>'Website URL', 'admin_email'=>'Admin Email'];
foreach ($_POST as $key=>$value) {
  $_POST[$key] = strip_tags($value);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') if (Router::post('submit-btn')=='submited'){
  $_lc=substr($_POST['gila_base'],-1);
  if($_lc!='/' && $_lc!='\\') $_POST['gila_base'].='/';
  foreach ($config_list as $key => $value) {
    Gila::setConfig($key,$_POST['gila_'.$key]);
  }
  Gila::config('default-controller',$_POST['gila_dc']);
  Gila::config('timezone',$_POST['gila_timezone']);
  Gila::config('language',$_POST['gila_language']);
  Gila::config('admin_logo',$_POST['gila_admin_logo']);
  Gila::config('favicon',$_POST['gila_favicon']);
  Gila::config('env',$_POST['gila_env']);
  Gila::config('check4updates',$_POST['gila_check4updates']);
  Gila::config('rewrite',$_POST['gila_rewrite']);
  Gila::config('user_register',$_POST['gila_user_register']);
  Gila::config('use_cdn',$_POST['gila_use_cdn']);
  Gila::config('use_webp',$_POST['gila_webp']);
  Gila::config('maxImgWidth',$_POST['gila_maxImgWidth']);
  Gila::config('maxImgHeight',$_POST['gila_maxImgHeight']);
  Gila::updateConfigFile();
  View::alert('success',__('_changes_updated'));
}
View::script('src/core/assets/admin/media.js');
View::script('src/core/lang/content/'.Gila::config('language').'.js');
?>

<div class="gm-12">
<?php View::alerts(); ?>
<form id="settings-form" method="post" action="<?=Gila::make_url('admin','settings')?>" class="g-form">
  <input type="hidden" name="submit-btn">
  <h2><?=__("Basic Settings")?></h2><hr>

<?php
foreach ($config_list as $key=>$value) if($value[0] != '.') { ?>
  <br><div class="gm-12">
  <label class="gm-4"><?=__($value)?></label>
  <input name="gila_<?=$key?>" value="<?=Gila::config($key)?>" class="gm-4" />
  </div>
<?php } ?>

  <br>
  <?php echo gForm::input('gila_user_register',["type"=>"switcher"],Gila::config('user_register'),__("New users can register")) ?>

  <br><div class="gm-12">
    <label class="gm-4"><?=__("Timezone")?></label>
    <select name="gila_timezone" value="<?=Gila::config('timezone')?>" class="gm-4">
    <?php
    foreach (DateTimeZone::listIdentifiers() as $value) {
      $sel = (Gila::config('timezone')==$value?'selected':'');
      echo '<option value="'.$value."\" $sel>".ucwords($value).'</option>';
    }
    ?>
    </select>
  </div>

  <br><div class="gm-12">
    <label class="gm-4"><?=__("Language")?></label>
    <select name="gila_language" value="<?=Gila::config('language')?>" class="gm-4">
    <?php
    $languages = include('src/core/lang/languages.php');
    foreach ($languages as $k=>$value) {
      $sel = (Gila::config('language')==$k?'selected':'');
      echo '<option value="'.$k."\" $sel>".ucwords($value).'</option>';
    }
    ?>
    </select>
  </div>

  <br><div class="gm-12">
    <label class="gm-4"><?=__("Admin Logo")?></label>
    <div class="gm-4" style="display:inline-flex"><span class="g-group" style="display:inline-block">
      <span class="btn g-group-item" style="width:28px" onclick="open_media_gallery('#m_admin_logo')"><i class="fa fa-image"></i></span>
      <span class="g-group-item"><input class="fullwidth" value="<?=Gila::config('admin_logo')?>" id="m_admin_logo" name="gila_admin_logo"></span>
    </span></div>
  </div>

  <br><div class="gm-12">
    <label class="gm-4"><?=__("Favicon")?></label>
    <div class="gm-4" style="display:inline-flex"><span class="g-group" style="display:inline-block">
      <span class="btn g-group-item" style="width:28px" onclick="open_media_gallery('#m_favicon')"><i class="fa fa-image"></i></span>
      <span class="g-group-item"><input class="fullwidth" value="<?=Gila::config('favicon')?>" id="m_favicon" name="gila_favicon"></span>
    </span></div>
  </div>

  <br>
  <a class="g-btn" onclick="document.getElementsByName('submit-btn')[0].value='submited'; document.getElementById('settings-form').submit();"><?=__("Submit")?></a>

  <h2><?=__("Advanced Settings")?></h2><hr>

  <br>
  <?php echo gForm::input('gila_use_cdn', ["type"=>"switcher"],Gila::config('use_cdn'), __("Use CDN")) ?>

  <br>
  <?php echo gForm::input('gila_rewrite', ["type"=>"switcher"],Gila::config('rewrite'), __("Pretty Urls")) ?>

  <br><div class="gm-12">
  <label class="gm-4"><?=__("Default Controller")?></label>
  <select name="gila_dc" value="<?=Gila::config('default-controller')?>" class="gm-4">
  <?php
  foreach (Gila::$controller as $k=>$value) if($value[0] != '.') if(!in_array($k,['cm','login','webhook','fm','lzld'])){
    $sel = (Gila::config('default-controller')==$k?'selected':'');
    echo '<option value="'.$k."\" $sel>".ucwords($k).'</option>';
  }
  ?>
  </select>
  </div>

  <br>
  <?php echo gForm::input('gila_env', ["type"=>"select","options"=>['pro'=>__('Production'),'dev'=>__('Development')]], Gila::config('env'), __("Environment")) ?>

  <br>
  <?php echo gForm::input('gila_check4updates', ["type"=>"switcher"], Gila::config('check4updates'), __("Check For Updates")) ?>

  <br>
  <?php echo gForm::input('gila_webp', ["type"=>"switcher"], Gila::config('use_webp'), __("Use WEBP")) ?>

  <br>
  <div class="gm-12">
  <label class="gm-4"><?=__("Max Media Upload")?> (px)</label>
  <input name="gila_maxImgWidth" value="<?=Gila::config('maxImgWidth')?>" type="number" class="gm-2" />
  &times;<input name="gila_maxImgHeight" value="<?=Gila::config('maxImgHeight')?>" type="number" class="gm-2" />
  </div>

  <br>
  <a class="g-btn" onclick="document.getElementsByName('submit-btn')[0].value='submited'; document.getElementById('settings-form').submit();"><?=__("Submit")?></a>
</form>
</div>
