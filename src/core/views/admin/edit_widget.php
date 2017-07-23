
<form id="widget_options_form">
<input type="hidden" value="<?=$widget->id?>" id='widget_id' name='widget_id'>
<div class="gm-12" style="display:inline-flex">
<div class="gm-6">
    <label class="gm-4">Widget Area</label>
    <select  id="widget_area" name="widget_area" value="<?=gila::config('default-controller')?>" class="gm-6 g-input">
        <?php
        foreach (gila::$widget_area as $value) {
            $sel = ($widget->area==$value?'selected':'');
            echo '<option value="'.$value."\" $sel>".ucwords($value).'</option>';
        }
        ?>
    </select>
</div>

<div class="gm-6">
    <label class="gm-4">Position</label>
    <input id="widget_pos" name="widget_pos" value="<?=$widget->pos?>" class="gm-6 g-input">
</div>
</div>
<hr>

<?php
global $db;
$widget_data = json_decode($db->value("SELECT data FROM widget WHERE id=? LIMIT 1;", $widget->id));
$widget_folder = 'src/'.gila::$widget[$widget->widget];
/*if(file_exists($widget_folder.'/edit.php'))
    include $widget_folder.'/edit.php';
else*/
    include $widget_folder.'/widget.php';

if(isset($options)) foreach($options as $key=>$op) {
    echo '<div class="gm-12">';
    echo '<label class="gm-4">'.(isset($op['title'])?$op['title']:ucwords($key)).'</label>';
    $ov = isset($widget_data->$key)?$widget_data->$key:'';

    if(isset($op['type'])) {
        if($op['type']=='select') {
            if(!isset($op['options'])) die("<b>Option $key require options</b>");
            echo '<select class="g-input gm-8" name="option['.$key.']">';
            foreach($op['options'] as $value=>$name) {
                echo '<option value="'.$value.'"'.($value==$ov?' selected':'').'>'.$name.'</option>';
            }
            echo '</select>';
        } else if($op['type']=='textarea') {
            echo '<textarea class="gm-8 codemirror-js" name="option['.$key.']">'.$ov.'</textarea>';
        }
    } else echo '<input class="g-input gm-8" name="option['.$key.']" value="'.$ov.'">';
    echo '</div><br>';
}
echo "</form>";
