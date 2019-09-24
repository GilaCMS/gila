<?php
$config_list = ['title'=>'Title', 'slogan'=>'Description', 'base'=>'Website URL', 'admin_email'=>'Admin Email'];
foreach ($_POST as $key=>$value) {
  $_POST[$key] = strip_tags($value);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') if (router::post('submit-btn')=='submited'){
  $_lc=substr($_POST['gila_base'],-1);
  if($_lc!='/' && $_lc!='\\') $_POST['gila_base'].='/';
  foreach ($config_list as $key => $value) {
    gila::setConfig($key,$_POST['gila_'.$key]);
  }
  gila::config('theme',$_POST['gila_theme']);
  gila::config('default-controller',$_POST['gila_dc']);
  gila::config('timezone',$_POST['gila_timezone']);
  gila::config('language',$_POST['gila_language']);
  gila::config('admin_logo',$_POST['gila_admin_logo']);
  gila::config('favicon',$_POST['gila_favicon']);
  gila::config('env',$_POST['gila_env']);
  gila::config('check4updates',$_POST['gila_check4updates']);
  gila::config('rewrite',$_POST['gila_rewrite']);
  gila::config('user_register',$_POST['gila_user_register']);
  gila::config('use_cdn',$_POST['gila_use_cdn']);
  gila::config('use_webp',$_POST['gila_webp']);
  gila::updateConfigFile();
  view::alert('success',__('_changes_updated'));
}
view::script('src/core/assets/admin/media.js');
view::script('src/core/lang/content/'.gila::config('language').'.js');
?>

<div class="gm-12">
    <?php view::alerts(); ?>
    <form id="settings-form" method="post" action="<?=gila::make_url('admin','settings')?>" class="g-form">
    <input type="hidden" name="submit-btn">

<h2><?=__("Basic Settings")?></h2><hr>

<?php
foreach ($config_list as $key=>$value) if($value[0] != '.') { ?>
    <br><div class="gm-12">
    <label class="gm-4"><?=__($value)?></label>
    <input name="gila_<?=$key?>" value="<?=gila::config($key)?>" class="gm-4" />
    </div>
<?php } ?>

<br>
<?php echo gForm::input('gila_user_register',["type"=>"switcher"],gila::config('user_register'),__("New users can register")) ?>

    <br><div class="gm-12">
    <label class="gm-4"><?=__("Theme")?></label>
    <select name="gila_theme" value="<?=gila::config('theme')?>" class="gm-4">
    <?php
    $theme_list=scandir('themes/');
    foreach ($theme_list as $value) if($value[0] != '.') if(count(explode('.',$value))==1) {
        $sel = (gila::config('theme')==$value?'selected':'');
        echo '<option value="'.$value."\" $sel>".ucwords($value).'</option>';
    }
    ?>
    </select>
    </div>

    <br><div class="gm-12">
        <label class="gm-4"><?=__("Timezone")?></label>
        <select name="gila_timezone" value="<?=gila::config('timezone')?>" class="gm-4">
        <?php
        foreach (DateTimeZone::listIdentifiers() as $value) {
            $sel = (gila::config('timezone')==$value?'selected':'');
            echo '<option value="'.$value."\" $sel>".ucwords($value).'</option>';
        }
        ?>
        </select>
    </div>

    <br><div class="gm-12">
        <label class="gm-4"><?=__("Language")?></label>
        <select name="gila_language" value="<?=gila::config('language')?>" class="gm-4">
        <?php
        $languages = ['en'=>'English','es'=>'Español','fr'=>'Français','de'=>'Deutsche','el'=>'Ελληνικά','et'=>'Eesti'];
        foreach ($languages as $k=>$value) {
            $sel = (gila::config('language')==$k?'selected':'');
            echo '<option value="'.$k."\" $sel>".ucwords($value).'</option>';
        }
        ?>
        </select>
    </div>

    <br><div class="gm-12">
        <label class="gm-4"><?=__("Admin Logo")?></label>
        <div class="gm-4" style="display:inline-flex"><span class="g-group" style="display:inline-block">
            <span class="btn g-group-item" style="width:28px" onclick="open_media_gallery('#m_admin_logo')"><i class="fa fa-image"></i></span>
            <span class="g-group-item"><input class="fullwidth" value="<?=gila::config('admin_logo')?>" id="m_admin_logo" name="gila_admin_logo"></span>
        </span></div>
    </div>

    <br><div class="gm-12">
        <label class="gm-4"><?=__("Favicon")?></label>
        <div class="gm-4" style="display:inline-flex"><span class="g-group" style="display:inline-block">
            <span class="btn g-group-item" style="width:28px" onclick="open_media_gallery('#m_favicon')"><i class="fa fa-image"></i></span>
            <span class="g-group-item"><input class="fullwidth" value="<?=gila::config('favicon')?>" id="m_favicon" name="gila_favicon"></span>
        </span></div>
    </div>

    <br>
    <a class="g-btn" onclick="document.getElementsByName('submit-btn')[0].value='submited'; document.getElementById('settings-form').submit();"><?=__("Submit")?></a>


    <h2><?=__("Advanced Settings")?></h2><hr>

    <br>
    <?php echo gForm::input('gila_use_cdn',["type"=>"switcher"],gila::config('use_cdn'),__("Use CDN")) ?>

    <br>
    <?php echo gForm::input('gila_rewrite',["type"=>"switcher"],gila::config('rewrite'),__("Pretty Urls")) ?>

    <br><div class="gm-12">
    <label class="gm-4"><?=__("Default Controller")?></label>
    <select name="gila_dc" value="<?=gila::config('default-controller')?>" class="gm-4">
    <?php
    foreach (gila::$controller as $k=>$value) if($value[0] != '.') if(!in_array($k,['cm','login','webhook','fm','lzld'])){
        $sel = (gila::config('default-controller')==$k?'selected':'');
        echo '<option value="'.$k."\" $sel>".ucwords($k).'</option>';
    }
    ?>
    </select>
    </div>

    <br>
    <?php echo gForm::input('gila_env',["type"=>"select","options"=>['pro'=>__('Production'),'dev'=>__('Development')]],gila::config('env'),__("Environment")) ?>

    <br>
    <?php echo gForm::input('gila_check4updates',["type"=>"switcher"],gila::config('check4updates'),__("Check For Updates")) ?>

    <br>
    <?php echo gForm::input('gila_webp',["type"=>"switcher"],gila::config('use_webp'),__("Use WEBP")) ?>

    <br>
    <a class="g-btn" onclick="document.getElementsByName('submit-btn')[0].value='submited'; document.getElementById('settings-form').submit();"><?=__("Submit")?></a>
    </form>
</div>
