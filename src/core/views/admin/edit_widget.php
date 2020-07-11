
<style>
.mce-window.mce-in {
  z-index: 99999 !important;
}
</style>
<form id="widget_options_form" class="g-form">
<input type="hidden" value="<?=$widget->id?>" id='widget_id' name='widget_id'>
<div class="gm-12" style="display:inline-flex;margin-bottom:8px;gap:1em">
<div class="gm-6">
  <label class="gm-4">Widget Area</label>
  <select  id="widget_area" name="widget_area" class="gm-6 g-input">
    <?php
    foreach (Gila::$widget_area as $value) {
      $sel = ($widget->area==$value?'selected':'');
      echo '<option value="'.$value."\" $sel>".ucwords($value).'</option>';
    }
    ?>
  </select>
</div>

<div class="gm-6">
  <label class="gm-4">Position</label>
  <input id="widget_pos" name="widget_pos" value="<?=htmlentities($widget->pos)?>" class="gm-6 g-input">
</div>
</div>

<div class="gm-12" style="display:inline-flex;margin-bottom:8px;gap:1em">
<div class="gm-6">
  <label class="gm-4">Title</label>
  <input id="widget_title" name="widget_title" value="<?=htmlentities($widget->title)?>" class="gm-6 g-input">
</div>

<div class="gm-6">
  <label class="gm-4">Active</label>
  <select id="widget_active" name="widget_active" value="" class="gm-6 g-input">
    <option value="0" <?=($widget->active?'':'selected')?>><?=__('No')?></option>
    <option value="1" <?=($widget->active?'selected':'')?>><?=__('Yes')?></option>
  </select>
</div>
</div>

<?php
global $db;
$widget_data = json_decode($db->value("SELECT data FROM widget WHERE id=? LIMIT 1;", $widget->id));
$widget_folder = 'src/'.Gila::$widget[$widget->widget];

$fields = include $widget_folder.'/widget.php';
if (isset($options)) {
  $fields = $options;
}

if (isset($fields)) {
  foreach ($fields as $key=>$op) {
    $values[$key] = isset($widget_data->$key)?$widget_data->$key:'';
  }
}
echo gForm::html($fields, $values, 'option[', ']');
echo "</form>";
