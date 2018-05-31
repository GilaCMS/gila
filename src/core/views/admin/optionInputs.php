<?php

foreach($options as $key=>$op) {
    echo '<div class="gm-12 row">';
    echo '<label class="gm-4">'.(isset($op['title'])?$op['title']:ucwords($key)).'</label>';
    $ov = $values[$key];
    if(!$ov) if(isset($op['default'])) $ov = $op['default'];
    
    if(isset($op['type'])) {
        if($op['type']=='select') {
            if(!isset($op['options'])) die("<b>Option $key require options</b>");
            echo '<select class="g-input gm-8" name="option['.$key.']">';
            foreach($op['options'] as $value=>$name) {
                echo '<option value="'.$value.'"'.($value==$ov?' selected':'').'>'.$name.'</option>';
            }
            echo '</select>';
        }
        if($op['type']=='postcategory') {
            echo '<select class="g-input gm-8" name="option['.$key.']">';
            $res=$db->get('SELECT id,title FROM postcategory;');
            echo '<option value=""'.(''==$ov?' selected':'').'>'.'[All]'.'</option>';
            foreach($res as $r) {
                echo '<option value="'.$r[0].'"'.($r[0]==$ov?' selected':'').'>'.$r[1].'</option>';
            }
            echo '</select>';
        }
        if($op['type']=='media') { ?>
            <div class="gm-8 g-group">
              <span class="btn g-group-item" style="width:28px" onclick="open_media_gallery('#m_<?=$key?>')"><i class="fa fa-image"></i></span>
              <span class="g-group-item"><input class="fullwidth" value="<?=$ov?>" id="m_<?=$key?>" name="option[<?=$key?>]"><span>
            </span></span></div>
        <?php }
        if($op['type']=='textarea') {
            echo '<textarea class="gm-8 codemirror-js" name="option['.$key.']">'.$ov.'</textarea>';
        }
    } else echo '<input class="g-input gm-8" name="option['.$key.']" value="'.$ov.'">';
    echo '</div><br>';
}
