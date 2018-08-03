<?php


class lzld extends controller
{
    function __construct ()
    {

    }

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

}
