<?php

namespace Gila;

class Form
{
  private static $html;
  private static $input_type;

  public static function posted($name = '*')
  {
    if ($_SERVER['REQUEST_METHOD']==='POST') {
      if (Session::key('_t'.$name)===$_POST['formToken']) {
        if ($name!=='*') {
          Session::unsetKey('_t'.$name);
        }
        return true;
      }
    }
    return false;
  }

  public static function verifyToken($check, $name = '*')
  {
    if (Session::key('_t'.$name)===$check) {
      return true;
    }
    return false;
  }

  public static function getToken($name = '*')
  {
    if ($v = Session::key('_t'.$name)) {
      return $v;
    }
    $chars = 'bcdfghjklmnprstvwxzaeiou123467890';
    $gsession = (string)Session::userId();
    for ($p = strlen($gsession); $p < 15; $p++) {
      $gsession .= $chars[mt_rand(0, 32)];
    }
    Session::key('_t'.$name, $gsession);
    return $gsession;
  }

  public static function hiddenInput($name = '*')
  {
    return '<input type="hidden" name="formToken" value="'.self::getToken($name).'">';
  }

  public static function html($fields, $values = [], $prefix = '', $suffix = '')
  {
    self::$html = '';
    self::initInputTypes();

    foreach ($fields as $key=>$op) {
      $ov = @$values[$key];
      if (!$ov) {
        if (isset($op['default'])) {
          $ov = $op['default'];
        }
      }
      self::$html .= self::input($prefix.$key.$suffix, $op, $ov, $key);
    }
    return self::$html;
  }

  public static function input($name, $op, $ov = '', $key = '')
  {
    self::initInputTypes();
    $type = @$op['input-type']?:@$op['type'];
    $type = @$op['input_type']?:$type;
    $html = '<div class="type-'.$type.'">';
    $label = ucwords(str_replace(['-','_'], ' ', $key));
    $label = isset($op['label'])?$op['label']:$label;
    $label = isset($op['title'])?$op['title']:$label;
    $label = Config::tr($label);
    if ($label=='') {
      $label='&nbsp;';
    }
    if (@$op['required'] === true) {
      $label .= ' *';
    }

    $html .= '<div class="g-label">'.$label;
    if (isset($op['helptext'])) {
      $html .= '<br><span style="font-weight:400;font-size:90%">'.$op['helptext'].'</span>';
    }
    $html .= '</div>';

    if ($type) {
      if (isset(self::$input_type[$type])) {
        $html .= self::$input_type[$type]($name, $op, $ov);
      } elseif (in_array($type, ['hidden','number','date','datetime-local','time','color','password','email','range'])) {
        $req = isset($op['required'])? ' required':'';
        if ($type==='datetime-local' && $ov) {
          $ov=date('Y-m-d\TH:i', is_string($ov)? strtotime($ov): $ov);
        }
        if ($type==='range') {
          $req = ' min='.($op['min']??0).' max='.($op['max']??10).' step='.($op['step']??1);
        }
        $html .= '<input class="g-input" name="'.$name.'" value="'.htmlspecialchars($ov).'" type="'.$type.'"'.$req.'>';
      } else {
        $placeholder = isset($op['placeholder'])? ' placeholder="'.$op['placeholder'].'"': '';
        $req = isset($op['required'])? ' required':'';
        $html .= '<input class="g-input" name="'.$name.'" value="'.htmlspecialchars($ov).'"'.$placeholder.$req.'>';
      }
    } else {
      $req = $op['required']? ' required':'';
      $value = !empty($ov)? 'value="'.htmlspecialchars($ov).'"': '';
      $html .= '<input class="g-input" name="'.$name.'" '.$value.$req.'>';
    }

    return $html . '</div>';
  }

  public static function addInputType($index, $value)
  {
    Config::addList($index, $value);
    if (!isset(self::$input_type)) {
      self::initInputTypes();
    }
    self::$input_type[$index] = $value;
  }

  public static function initInputTypes()
  {
    if (isset(self::$input_type)) {
      return;
    }

    self::$input_type = [
      "select"=> function ($name, $field, $ov) {
        $html = '<select class="g-input" name="'.$name.'">';
        foreach ($field['options'] as $value=>$name) {
          $html .= '<option value="'.$value.'"'.($value==$ov?' selected':'').'>'.$name.'</option>';
        }
        return $html . '</select>';
      },
      "meta"=> function ($name, $field, $ov) {
        if (@$field['meta-csv']==true || @$field['meta_csv']==true) {
          return '<input class="g-input" placeholder="values seperated by comma" name="'.$name.'" value="'.htmlspecialchars($ov).'"/>';
        }
        if (is_string($ov)) {
          $ov = explode(',', $ov);
        }
        $html = '<g-multiselect value="'.htmlspecialchars(json_encode($ov??[])).'"';
        return $html.= 'options="'.htmlspecialchars(json_encode($field['options'])).'" name="'.$name.'">';
      },
      "keywords"=> function ($name, $field, $ov) {
        if (is_string($ov)) {
          $ov = explode(',', $ov);
        }
        $html = '<input-keywords value="'.htmlspecialchars(json_encode($ov??[])).'"';
        return $html.= ' name="'.$name.'">';
      },
      "select2"=> function ($name, $field, $ov) { //DEPRECATED
        if (is_string($ov)) {
          $ov = explode(',', $ov);
        }
        $html = '<select class="g-input select2" multiple name="'.$name.'[]">';
        foreach ($field['options'] as $value=>$name) {
          $html .= '<option value="'.$value.'"'.(in_array($value, $ov)?' selected':'').'>'.$name.'</option>';
        }
        return $html . '</select>';
      },
      "role"=> function ($name, $field, $ov) {
        global $db;
        if (is_string($ov)) {
          $ov = explode(',', $ov);
        }
        $getOptions = $db->get("SELECT `id`,`userrole` FROM userrole WHERE `level`<=".User::level(Session::userId()));
        foreach ($getOptions as $op) {
          $options[$op[0]] = $op[1];
        }
        $html = '<g-multiselect value="'.htmlspecialchars(json_encode($ov??[])).'"';
        return $html.= 'options="'.htmlspecialchars(json_encode($options)).'" name="'.$name.'">';
      },
      "radio"=> function ($name, $field, $ov) {
        $html = '<div class="g-radio g-input" style="padding: var(--main-padding) 0; width: max-content;">';
        foreach ($field['options'] as $value=>$display) {
          $id = 'radio_'.$name.'_'.$value;
          $html .= '<input name="'.$name.'" type="radio" value="'.$value.'"';
          $html .= ($value==$ov?' checked':'').' id="'.$id.$value.'">';
          $html .= '<label for="'.$id.$value.'">'.$display.'</label>';
        }
        return $html . '</div>';
      },
      "postcategory"=> function ($name, $field, $ov) {
        global $db;
        $html = '<select class="g-input" name="'.$name.'">';
        $res=$db->get('SELECT id,title FROM postcategory;');
        $html .= '<option value=""'.(''==$ov?' selected':'').'>'.'[All]'.'</option>';
        foreach ($res as $r) {
          $html .= '<option value="'.$r[0].'"'.($r[0]==$ov?' selected':'').'>'.$r[1].'</option>';
        }
        return $html . '</select>';
      },
      "comments"=> function ($name, $field, $ov) {
        $form = isset($field['content'])? ' form="'.$field['content'].'-edit-item-form"': '';
        return '<input-comments name="'.$name.'" fieldname="'.$field['fieldname'].'" username="'.Session::key('user_name').'" value="'.htmlspecialchars($ov??'[]').'"'.$form.'>';
      },
      "media2"=> function ($name, $field, $ov) {
        $id = 'm_'.str_replace(['[',']'], '_', $name);
        $ov = htmlspecialchars($ov);
        return '<input-media name="'.$name.'" value="'.$ov.'">';
      },
      "tree-select"=> function ($name, $field, $ov) {
        return '<tree-select name="'.$name.'" value="'.$ov.'">';
      },
      "palette"=> function ($name, $field, $ov) {
        $id = 'm_'.str_replace(['[',']'], '_', $name);
        if(empty($ov)) {
          $ov = json_encode(end($field['palettes']));
        }
        $field['palettes'][] = json_decode($ov,true);
        $ov = htmlspecialchars($ov);
        $pal = $field['palettes']? htmlspecialchars(json_encode($field['palettes'])): '';
        $labels = $field['labels']? htmlspecialchars(json_encode($field['labels'])): '';
        return '<color-palette name="'.$name.'" value="'.$ov.'" palettes="'.$pal.'" labels="'.$labels.'">';
      },
      "media-gallery"=> function ($name, $field, $ov) {
        $id = 'm_'.str_replace(['[',']'], '_', $name);
        if (!is_array(json_decode($ov))) {
          $ov = explode(',', $ov);
          for ($i=count($ov);$i<$field['max'];$i++) {
            array_push($ov, '');
          }
          $ov = json_encode($ov);
        }
        $ov = htmlspecialchars($ov);
        return '<input-gallery name="'.$name.'" value="'.$ov.'">';
      },
      "media"=> function ($name, $field, $ov) {
        $id = 'm_'.str_replace(['[',']'], '_', $name);
        $ov = htmlspecialchars($ov);
        return '<div class="g-group">
          <span class="g-btn g-group-item" onclick="open_media_gallery(\'#'.$id.'\')"><i class="fa fa-image"></i></span>
          <span class="g-group-item"><input class="g-input fullwidth" value="'.$ov.'" id="'.$id.'" name="'.$name.'"><span>
        </span></span></div>';
      },
      "key"=> function ($name, $field, $ov) {
        $id = 'm_'.str_replace(['[',']'], '_', $name);
        return '<div class="g-group">
          <span class="btn g-group-item" onclick="open_select_from_table(\'#'.$id.'\',\''.$field['table'].'\',\''.$field['title'].'\')"><i class="fa fa-key"></i></span>
          <span class="g-group-item"><input class="fullwidth" value="'.($ov??0).'" id="'.$id.'" name="'.$name.'"><span>
        </span></span></div>';
      },
      "textarea"=> function ($name, $field, $ov) {
        return '<textarea class="g-input fullwidth" name="'.$name.'" style="resize:vertical;">'.htmlspecialchars($ov).'</textarea>';
      },
      "codemirror"=> function ($name, $field, $ov) {
        return '<textarea class="g-input fullwidth codemirror-js" name="'.$name.'">'.htmlspecialchars($ov).'</textarea>';
      },
      "tinymce"=> function ($name, $field, $ov) {
        $id = 'm_'.str_replace(['[',']'], '_', $name);
        return '<textarea class="g-input fullwidth tinymce" id="'.$id.'" name="'.$name.'">'.htmlspecialchars($ov).'</textarea>';
      },
      "paragraph"=> function ($name, $field, $ov) {
        $id = 'm_'.str_replace(['[',']'], '_', $name);
        return '<textarea class="g-input fullwidth tinymce" id="'.$id.'" name="'.$name.'">'.htmlentities($ov).'</textarea>';
      },
      "vue-editor"=> function ($name, $field, $ov) {
        $id = 'm_'.str_replace(['[',']'], '_', $name);
        return '<vue-editor id="'.$id.'" name="'.$name.'" text="'.htmlentities($ov).'"></vue-editor>';
      },
      "language"=> function ($name, $field, $ov) {
        $html = '<select class="g-input" name="'.$name.'">';
        $res = include 'src/core/lang/languages.php';
        foreach ($res as $key=>$r) {
          $html .= '<option value="'.$key.'"'.($key==$ov?' selected':'').'>'.$r.'</option>';
        }
        return $html . '</select>';
      },
      "checkbox"=> function ($name, $field, $ov) {
        return self::$input_type['switch']($name, $field, $ov);
      },
      "switch"=> function ($name, $field, $ov) {
        if ($ov==1) {
          $checked=["","checked"];
        } else {
          $checked=["checked",""];
        }
        return '<div class="g-switch">
        <input name="'.$name.'" type="radio" value="0" id="chsw_'.$name.'0" '.$checked[0].'>
        <input name="'.$name.'" type="radio" value="1" id="chsw_'.$name.'1" '.$checked[1].'>
        <div class="g-slider"></div>
        </div>
        ';
      },
      "list"=> function ($name, $field, $ov) {
        $fieldset = htmlspecialchars(json_encode(array_keys($field['fields'])));
        $value = json_decode($ov) ? htmlspecialchars($ov) : '[]';
        return '<input-list style="width:100%;border:1px solid var(--main-border-color);" name="'.$name.'" fieldset="'.$fieldset.'" value="'.$value.'"></input-list>';
      },
      "template"=> function ($name, $field, $ov) {
        global $db;
        $html = '<select class="g-input" name="'.$name.'">';
        $templates = View::getTemplates($field['template']);
        $html .= '<option value=""'.(''==$ov?' selected':'').'>'.'[Default]'.'</option>';
        foreach ($templates as $template) {
          $html .= '<option value="'.$template.'"'.($template==$ov?' selected':'').'>'.ucwords($template).'</option>';
        }
        return $html . '</select>';
      }
    ];

    foreach (Config::getList('input-type') as $type=>$value) {
      self::$input_type[$type] = $value;
    }
    /* CONTENT
    if($type=='content') {
      $table = $op['table'];
      $tablesrc = explode('.', Config::$content[$table])[0];
      include __DIR__.'/content.php';
    }*/
  }
}

class_alias('Gila\\Form', 'gForm');
