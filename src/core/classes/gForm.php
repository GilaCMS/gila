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
            self::$html .= self::input($prefix.$key.$suffix, $op, $ov, $key);
        }
        return self::$html;
    }

    static function input($name,$op,$ov = '', $key = '')
    {
        self::initInputTypes();
        $type = @$op['input-type']?:@$op['type'];
        $html = '<div class="gm-12 row type-'.$type.'">';
        $label = isset($op['label'])?$op['label']:ucwords($key);
        $label = isset($op['title'])?$op['title']:$label;
        $label = __($label);
        if($label=='') $label='&nbsp;';
        if(@$op['required'] == true) $label .= ' *';

        $html .= '<label class="gm-4">'.$label;
        if(isset($op['helptext'])) $html .= '<br><span style="font-weight:400;font-size:90%">'.$op['helptext'].'</span>';
        $html .= '</label>';

        if($type) {
            if(isset(self::$input_type[$type])) {
                $html .= self::$input_type[$type]($name,$op,$ov);
            } else if(in_array($type,['hidden','date','time','color','password','email'])) {
            /* OTHER TYPES */
                $html .= '<input class="g-input" name="'.$name.'" value="'.$ov.'" type="'.$type.'">';
            } else {
                $html .= '<input class="g-input" name="'.$name.'" value="'.$ov.'">';
            }
        } else {
            $html .= '<input class="g-input" name="'.$name.'" value="'.$ov.'">';
        }

        return $html . '</div>';
    }

    static function addInputType ($index, $value)
    {
        if(!isset(self::$input_type)) self::initInputTypes();
        self::$input_type[$index] = $value;
    }

    static function initInputTypes()
    {
        if(isset(self::$input_type)) return;

        self::$input_type = [
            "select"=> function($name,$field,$ov) {
                //if(!isset($field['options'])) die("<b>Option $key require options</b>");
                $html = '<select class="g-input" name="'.$name.'">';
                foreach($field['options'] as $value=>$name) {
                    $html .= '<option value="'.$value.'"'.($value==$ov?' selected':'').'>'.$name.'</option>';
                }
                return $html . '</select>';
            },
            "meta"=> function($name,$field,$ov) {
                if(is_string($ov)) $ov = explode(',',$ov);
                if(@$field['meta-csv']==true) {
                    return '<input class="g-input" placeholder="values seperated by comma" name="'.$name.'" value="'.$ov.'"/>';
                }
                $html = '<select class="g-input select2" multiple name="'.$name.'[]">';
                foreach($field['options'] as $value=>$name) {
                    $html .= '<option value="'.$value.'"'.(in_array($value,$ov)?' selected':'').'>'.$name.'</option>';
                }
                return $html . '</select>';
            },
            "radio"=> function($name,$field,$ov) {
                $html = '<div class="g-radio g-input">';
                foreach($field['options'] as $value=>$display) {
                    $id = 'radio_'.$name.'_'.$value;
                    $html .= '<input name="'.$name.'" type="radio" value="'.$value.'"'.($value==$ov?' checked':'').' id="'.$id.'" '.$checked[0].'>';
                    $html .= '<label for="'.$id.'">'.$display.'</label>';
                }
                return $html . '</div>';
            },
            "postcategory"=> function($name,$field,$ov) {
                global $db;
                $html = '<select class="g-input" name="'.$name.'">';
                $res=$db->get('SELECT id,title FROM postcategory;');
                $html .= '<option value=""'.(''==$ov?' selected':'').'>'.'[All]'.'</option>';
                foreach($res as $r) {
                    $html .= '<option value="'.$r[0].'"'.($r[0]==$ov?' selected':'').'>'.$r[1].'</option>';
                }
                return $html . '</select>';
            },
            "media"=> function($name,$field,$ov) {
                $id = 'm_'.str_replace(['[',']'], '_', $name);    
                return '<div class="g-group">
                  <span class="btn g-group-item" style="width:28px" onclick="open_media_gallery(\'#'.$id.'\')"><i class="fa fa-image"></i></span>
                  <span class="g-group-item"><input class="fullwidth" value="'.$ov.'" id="'.$id.'" name="'.$name.'"><span>
                </span></span></div>';
            },
            "key"=> function($name,$field,$ov) {
                $id = 'm_'.str_replace(['[',']'], '_', $name);    
                return '<div class="g-group">
                  <span class="btn g-group-item" style="width:28px" onclick="open_select_row(\'#'.$id.'\',\''.$field['table'].'\')"><i class="fa fa-key"></i></span>
                  <span class="g-group-item"><input class="fullwidth" value="'.$ov.'" id="'.$id.'" name="'.$name.'"><span>
                </span></span></div>';
            },
            "textarea"=> function($name,$field,$ov) {
                return '<textarea class="codemirror-js" name="'.$name.'">'.$ov.'</textarea>';
            },
            "tinymce"=> function($name,$field,$ov) {
                return '<textarea class="tinymce" id="'.$name.'" name="'.$name.'">'.$ov.'</textarea>';
            },
            "checkbox"=> function($name,$field,$ov) {
                return self::$input_type['switcher']($name,$field,$ov);
            },
            "switcher"=> function($name,$field,$ov) {
                if($ov==1) $checked=["","checked"]; else $checked=["checked",""];
                return '<div class="g-switcher ">
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
        if($type=='content') {
            $table = $op['table'];
            $tablesrc = explode('.',gila::$content[$table])[0];
            include __DIR__.'/content.php';
        }*/
    }
}
