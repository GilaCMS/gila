<?php


class lzld extends Controller
{

  function indexAction ()
  {

  }

  function widgetAction ($id)
  {
    global $widget_data;
    $widget = Gila\Widget::getById($id);

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
    echo Menu::getHtml(Gila::$amenu, $_GET['base'] ?? 'admin');
  }

}
