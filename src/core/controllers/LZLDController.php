<?php
use Gila\Config;
use Gila\View;
use Gila\Menu;
use Gila\Widget;

class LZLDController extends Gila\Controller
{
  public function indexAction()
  {
  }

  public function widgetAction($id)
  {
    global $widget_data;
    $widget = Widget::getById($id);

    if ($widget) {
      if ($widget->active==1) {
        $widget_data = json_decode($widget->data);
        @$widget_data->widget_id = $id;
        View::widgetBody($widget->widget, $widget_data);
      }
    }
  }

  public function widget_areaAction($area)
  {
    View::widgetArea($area);
  }

  public function thumbAction()
  {
    $size = $_GET['media_thumb'] ?? ($_GET['size'] ?? 80);
    $file = View::thumb($_GET['src'], "thumb$size/", $size);
    Image::readfile($file);
  }

  public function amenuAction()
  {
    echo Menu::getHtml(Config::$amenu, $_GET['base'] ?? 'admin');
  }

  public function notificationSetReadAction()
  {
    UserNotification::setRead($_POST['id']);
  }
}
