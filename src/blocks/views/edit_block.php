
<form id="widget_options_form" class="g-form">
<input type="hidden" value="<?=$widget_id?>" id='widget_id' name='widget_id'>
<?php
global $db;
$widget_data = [];
$widget_folder = 'src/'.Gila\Gila::$widget[$type];
$fields = include $widget_folder.'/widget.php';
if (isset($options)) {
  $fields = $options;
}

if ($id!=='new') {
  $widget_data = $widgets[$pos];
}

if (isset($fields)) {
  foreach ($fields as $key=>$op) {
    $values[$key] = $widget_data[$key]?? $op[$key]['default']?? '';
  }
}

echo Gila\Form::html($fields, $values, 'option[', ']');
echo "</form>";
