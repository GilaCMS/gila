<?php


class lzld extends controller
{

  function indexAction ($x)
  {

  }

  function widgetAction ($id)
  {
    global $widget_data;
    $widget = core\models\widget::getById($id);

    if ($widget) if ($widget->active==1) {
      $widget_data = json_decode($widget->data);
      @$widget_data->widget_id = $id;
      view::widget_body($widget->widget, $widget_data);
    }
  }

  function widget_areaAction ($area)
  {
    view::widget_area($area);
  }

  function thumbAction ()
  {
    $file = $_GET['src'];
    $ext = explode('.',$file);
    $ext = $ext[count($ext)-1];
    $size = (int)$_GET['media_thumb'] ?? 80;
    $file = view::thumb($file, 'media_thumb/', $size);

    if (file_exists($file)) {
      $imageInfo = getimagesize($file);
      ob_end_clean();
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
          if($ext=='svg') echo file_get_contents($file);
          exit;
          break;
      }

      header('Content-Length: ' . filesize($file));
      readfile($file);
    } else {
      http_response_code(404);
    }

  }

}
