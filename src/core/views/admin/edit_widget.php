
<style>
.mce-window.mce-in {
  z-index: 99999 !important;
}
.tox-dialog-wrap {
  z-index: 99999 !important;
}
</style>
<form id="widget_options_form" class="g-form">
<input type="hidden" value="<?=$widget->id?>" id='widget_id' name='widget_id'>
<div class="gm-12" style="display:inline-flex;margin-bottom:8px;gap:1em">
<div class="gm-6">
  <label class="gm-4"><?=__('Widget Area')?></label>
  <select  id="widget_area" name="widget_area" class="gm-6 g-input">
    <?php
    foreach (Config::$widget_area as $value) {
      $sel = ($widget->area==$value?'selected':'');
      echo '<option value="'.$value."\" $sel>".ucfirst($value).'</option>';
    }
    ?>
  </select>
</div>

<div class="gm-6">
  <label class="gm-4"><?=__('Position')?></label>
  <input id="widget_pos" name="widget_pos" value="<?=htmlentities($widget->pos)?>" class="gm-6 g-input">
</div>
</div>

<div class="gm-12" style="display:inline-flex;margin-bottom:8px;gap:1em">
<div class="gm-6">
  <label class="gm-4">Title</label>
  <input id="widget_title" name="widget_title" value="<?=htmlentities($widget->title)?>" class="gm-6 g-input">
</div>

<div class="gm-6">
  <label class="gm-4"><?=__('Active')?></label>
  <select id="widget_active" name="widget_active" value="" class="gm-6 g-input">
    <option value="0" <?=($widget->active?'':'selected')?>><?=__('No')?></option>
    <option value="1" <?=($widget->active?'selected':'')?>><?=__('Yes')?></option>
  </select>
</div>
</div>

<?php

if ($languages = Config::get('languages')) {
  ?>
<div class="gm-6">
  <label class="gm-4"><?=__('Language')?></label>
  <select  id="widget_language" name="widget_language" class="gm-6 g-input">
    <?php
    echo '<option value="">*</option>';
  foreach ($languages as $language) {
    $sel = ($widget->language==$language?'selected':'');
    echo '<option value="'.$language."\" $sel>".strtoupper($language).'</option>';
  } ?>
  </select>
</div>
<?php
}

$widget_data = json_decode(Gila\DB::value("SELECT data FROM widget WHERE id=? LIMIT 1;", $widget->id));
$fields = Gila\Widget::getFields($widget->widget);

if (isset($fields)) {
  foreach ($fields as $key=>$op) {
    $values[$key] = isset($widget_data->$key)?$widget_data->$key:'';
  }
}
echo Form::html($fields, $values, 'option[', ']');
echo "</form>";
