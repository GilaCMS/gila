<?php

class gForm
{
    static private $html;
    static private $input_type;


    function html ($fields, $values = [], $prefix = '', $suffix = '')
    {
        self::$html = '';
        self::initInputTypes();

        foreach($fields as $key=>$op) {
            $ov = @$values[$key];
            if(!$ov) if(isset($op['default'])) $ov = $op['default'];
            self::$html .= self::input($prefix.$key.$suffix, $op, $ov, $key).'<br>';
        }
        return self::$html;
    }

    static function input($name,$op,$ov = '', $key = '')
    {
        self::initInputTypes();
        $html = '<div class="gm-12 row">';
        $label = isset($op['label'])?$op['label']:ucwords($key);
        $label = isset($op['title'])?$op['title']:$label;
        $label = __($label);
        if($label=='') $label='&nbsp;';
        if(@$op['required'] == true) $label .= ' *';

        $html .= '<label class="gm-4">'.$label;
        if(isset($op['helptext'])) $html .= '<br><span style="font-weight:400;font-size:90%">'.$op['helptext'].'</span>';
        $html .= '</label>';

        if(isset($op['type'])) {
            if(isset(self::$input_type[$op['type']])) {
                $html .= self::$input_type[$op['type']]($name,$op,$ov);
            } else if(in_array($op['type'],['hidden','date','time','color','password','email'])) {
            /* OTHER TYPES */
                $html .= '<input class="g-input g-m-8" name="'.$name.'" value="'.$ov.'" type="'.$op['type'].'">';
            } else {
                $html .= '<input class="g-input g-m-8" name="'.$name.'" value="'.$ov.'">';
            }
        } else {
            $html .= '<input class="g-input g-m-8" name="'.$name.'" value="'.$ov.'">';
        }

        return $html . '</div>';
    }

    static function initInputTypes()
    {
        if(isset(self::$input_type)) return;

        self::$input_type = [
            "select"=> function($name,$field,$ov) {
                //if(!isset($field['options'])) die("<b>Option $key require options</b>");
                $html = '<select class="g-input g-m-8" name="'.$name.'">';
                foreach($field['options'] as $value=>$name) {
                    $html .= '<option value="'.$value.'"'.($value==$ov?' selected':'').'>'.$name.'</option>';
                }
                return $html . '</select>';
            },
            "meta"=> function($name,$field,$ov) {
                if(@$field['meta-csv']==true) {
                    return '<input class="g-input g-m-8" placeholder="values seperated by comma" name="'.$name.'" value="'.$ov.'"/>';
                }
                $html = '<select class="g-input g-m-8 select2" multiple name="'.$name.'">';
                foreach($field['options'] as $value=>$name) {
                    $html .= '<option value="'.$value.'"'.($value==$ov?' selected':'').'>'.$name.'</option>';
                }
                return $html . '</select>';
            },
            "radio"=> function($name,$field,$ov) {
                $html = '<div class="g-m-8 g-radio">';
                foreach($field['options'] as $value=>$display) {
                    $id = 'radio_'.$name.'_'.$value;
                    $html .= '<input name="'.$name.'" type="radio" value="'.$value.'"'.($value==$ov?' checked':'').' id="'.$id.'" '.$checked[0].'>';
                    $html .= '<label for="'.$id.'">'.$display.'</label>';
                }
                return $html . '</div>';
            },
            "postcategory"=> function($name,$field,$ov) {
                global $db;
                $html = '<select class="g-input g-m-8" name="'.$name.'">';
                $res=$db->get('SELECT id,title FROM postcategory;');
                $html .= '<option value=""'.(''==$ov?' selected':'').'>'.'[All]'.'</option>';
                foreach($res as $r) {
                    $html .= '<option value="'.$r[0].'"'.($r[0]==$ov?' selected':'').'>'.$r[1].'</option>';
                }
                return $html . '</select>';
            },
            "media"=> function($name,$field,$ov) {
                return '<div class="g-m-8 g-group">
                  <span class="btn g-group-item" style="width:28px" onclick="open_media_gallery(\'#m_'.$key.'\')"><i class="fa fa-image"></i></span>
                  <span class="g-group-item"><input class="fullwidth" value="'.$ov.'" id="m_'.$key.'" name="'.$name.'"><span>
                </span></span></div>';
            },
            "textarea"=> function($name,$field,$ov) {
                return '<textarea class="g-m-8 codemirror-js" name="'.$name.'">'.$ov.'</textarea>';
            },
            "checkbox"=> function($name,$field,$ov) {
                return self::$input_type['switcher']($name,$field,$ov);
            },
            "switcher"=> function($name,$field,$ov) {
                if($ov==1) $checked=["","checked"]; else $checked=["checked",""];
                return '<div class="g-switcher g-m-8">
                <input name="'.$name.'" type="radio" value="0" id="chsw_'.$name.'" '.$checked[0].'>
                <input name="'.$name.'" type="radio" value="1" id="chsw_'.$name.'" '.$checked[1].'>
                <div class="g-slider"></div>
                </div>
                ';
            },
            "list"=> function($name,$field,$ov) {
                $fieldset = htmlspecialchars(json_encode(array_keys($field['fields'])));
                $value = htmlspecialchars($ov);
                return '<input-list style="width:100%;border:1px solid var(--main-border-color);" name="'.$name.'" fieldset="'.$fieldset.'" value="'.$value.'"></input-list>';
            }
        ];
        /* CONTENT
        if($op['type']=='content') {
            $table = $op['table'];
            $tablesrc = explode('.',gila::$content[$table])[0];
            include __DIR__.'/content.php';
        }*/
    }
}
