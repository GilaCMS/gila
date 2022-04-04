
<form id="widget_options_form" class="g-form">
<input type="hidden" value="<?=$widget_id?>" id='widget_id' name='widget_id'>
<?php
$widget_data = [];
$fields = Gila\Widget::getFields($type);

if ($id!=='new') {
  $widget_data = $widgets[$pos];
}

if (isset($fields)) {
  foreach ($fields as $key=>$op) {
    if ($id!=='new') {
      unset($fields[$key]['default']);
    }
    $values[$key] = $widget_data[$key] ?? '';
  }
}

echo Gila\Form::html($fields, $values, 'option[', ']');
echo "</form>";
