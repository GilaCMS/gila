  <h2><?=__("Advanced Settings")?></h2><hr>

  <br>
  <?php echo Form::input('gila_use_cdn', ["type"=>"switch"], Config::get('use_cdn'), __("CDN")) ?>

  <br><div class="gm-12">
  <label class="g-label gm-4"><?=__("Default Controller")?></label><select name="gila_dc" value="<?=Config::get('default-controller')?>" class="gm-4">
  <?php
  foreach (Router::$controllers as $k=>$value) {
    if ($value[0] != '.') {
      if (!in_array($k, ['cm','user','webhook','fm','lzld','blocks'])) {
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
  <div class="">
  <label class="g-label gm-4"><?=__("Max Media Upload")?> (px)</label><div>
  <input name="gila_maxImgWidth" value="<?=Config::get('maxImgWidth')?>" type="number" class="gm-2" style="width:120px"/>
  &times;
  <input name="gila_maxImgHeight" value="<?=Config::get('maxImgHeight')?>" type="number" class="gm-2" style="width:120px" />
  </div>
  </div>

  <br>
  <?php echo Form::input('gila_utk_level', ["type"=>"select","options"=>[0=>'0',1=>'1',2=>'2',3=>'3',4=>'4',5=>'5',6=>'6',7=>'7',8=>'8',9=>'9',10=>'10']], 10, __("Unique Token Key")) ?>

  <br>
  <?php echo Form::input('gila_locale', ["type"=>"text","placeholder"=>"en_US.UTF-8"], null, __("Locale")) ?>
  
  <br>
  <div>
    <a class="g-btn" style="min-width:unset" onclick="save_settings()"><?=__("Submit")?></a>
  </div>