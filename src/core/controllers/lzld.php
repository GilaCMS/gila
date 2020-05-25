<?php


class lzld extends Controller
{

  function indexAction ()
  {

  }

  function widgetAction ($id)
  {
    global $widget_data;
    $widget = core\models\Widget::getById($id);

    if ($widget) if ($widget->active==1) {
      $widget_data = json_decode($widget->data);
      @$widget_data->widget_id = $id;
      View::widgetBody($widget->widget, $widget_data);
    }
  }

  function widget_areaAction ($area)
  {
    View::widgetArea($area);
  }

  function thumbAction ()
  {
    $file = $_GET['src'];
    $ext = explode('.', $file);
    $ext = $ext[count($ext)-1];
    $size = $_GET['media_thumb'] ?? ($_GET['size'] ?? 80);
    $file = View::thumb($file, "thumb$size/", $size);

    if (file_exists($file)) {
      ob_end_clean();
      header('Content-Length: '.filesize($file));
      $imageInfo = getimagesize($file);
      switch ($imageInfo[2]) {
        case IMAGETYPE_JPEG:
          header("Content-Type: image/jpeg");
          break;
        case IMAGETYPE_WEBP:
          header("Content-Type: image/webp");
          break;
        case IMAGETYPE_GIF:
          header("Content-Type: image/gif");
          break;
        case IMAGETYPE_PNG:
          header("Content-Type: image/png");
          break;
        default:
          if($ext=='svg' && 
              (substr($file,0,7) == 'assets/' || substr($file,0,4) == 'src/')) {
            header("Content-Type: image/svg+xml");
            echo file_get_contents($file);
          }
          return;
          break;
      }
      readfile($file);
    } else {
      http_response_code(404);
    }

  }

  function amenuAction () {
    $userId = Session::userId();
    $base = $_GET['base'] ?? 'admin';
    foreach (Gila::$amenu as $key => $value) {
      if(isset($value['access'])) if(!Gila::hasPrivilege($value['access'])) continue;
      if(isset($value['icon'])) $icon = 'fa-'.$value['icon']; else $icon='';
      $url = $value[1]=='#'? Gila::url($base.'#'): $value[1]; 
      echo "<li><a href='".$url."'><i class='fa {$icon}'></i>";
      echo " <span>".__("$value[0]")."</span></a>";
      if(isset($value['children'])) {
        echo "<ul class=\"dropdown\">";
        foreach ($value['children'] as $subkey => $subvalue) {
          if(isset($subvalue['access'])) if(!Gila::hasPrivilege($subvalue['access'])) continue;
          if(isset($subvalue['icon'])) $icon = 'fa-'.$subvalue['icon']; else $icon='';
          echo "<li><a href='".Gila::url($subvalue[1])."'><i class='fa {$icon}'></i> ".__("$subvalue[0]")."</a></li>";
        }
        echo "</ul>";
      }
      echo "</li>";
    }
  }

}
