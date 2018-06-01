<?php
use core\models\page as page;

class menuItemTypes
{
    static $itemTypes;

    function __construct ()
    {

    }

    static function defaultData()
    {
        global $db;
        $widget_data = (object) array('type'=>'menu','children' => []);
        $widget_data->children[] = ['type'=>'link','url'=>'','name'=>__('Home')];

        $ql = "SELECT id,title FROM postcategory;";
        $pages = $db->get($ql);
        foreach ($pages as $p) {
            $widget_data->children[] = ['type'=>"postcategory",'id'=>$p[0]];
        }

        foreach (page::genPublished() as $p) {
            $widget_data->children[] = ['type'=>'page','id'=>$p[0]];
        }

        return (array) $widget_data;
    }

    static function getItemTypes()
    {
        if(!isset(self::$itemTypes)) self::initItemTypes();
        return self::$itemTypes;
    }

    static function addItemType ($index, $value)
    {
        if(!isset(self::$itemTypes)) self::initItemTypes();
        self::$itemTypes[$index] = $value;
    }

    static function initItemTypes()
    {
        global $db;
        $pages = page::genPublished();
        $pageOptions = "";
        foreach ($pages as $p) {
            $pageOptions .= "<option value=\"{$p['id']}\">{$p['title']}</option>";
        }

        $ql = "SELECT id,title FROM postcategory;";
        $cats = $db->get($ql);
        $postcategoryOptions = "";
        foreach ($cats as $p) {
            $postcategoryOptions .= "<option value=\"{$p[0]}\">{$p[1]}</option>";
        }

        $widgetOptions = "";
        foreach (array_keys(gila::$widget) as $w) {
            $widgetOptions .= "<option value=\"$w\">$w</option>";
        }

        self::$itemTypes = [
            "link"=>[
                "data"=>[
                    "type"=>"link",
                    "name"=>"New Link",
                    "url"=>"#"
                ],
                "template"=>"<input v-model=\"model.name\" class=\"g-input\" placeholder=\"Name\"><i class=\"fa fa-chevron-right\"></i> <input v-model=\"model.url\" class=\"g-input\" placeholder=\"URI\">"
            ],
            "page"=>[
                "data"=>[
                    "type"=>"page",
                    "id"=>1
                ],
                "template"=>"<select class=\"g-input\" v-model=\"model.id\">$pageOptions</select>"
            ],
            "postcategory"=>[
                "data"=>[
                    "type"=>"postcategory",
                    "id"=>1
                ],
                "template"=>"<select class=\"g-input\" v-model=\"model.id\">$postcategoryOptions</select>"
            ],
            "widget"=>[
                "data"=>[
                    "type"=>"widgets",
                    "name"=>"Widget",
                    "widget"=>""
                ],
                "template"=>"<input v-model=\"model.name\" class=\"g-input\" placeholder=\"Name\"><select class=\"g-input\" v-model=\"model.widget\">$widgetOptions</select>",
            ],
            "dir"=>[
                "data"=>[
                    "type"=>"dir",
                    "name"=>"New Directory",
                    "children"=>[]
                ],
                "template"=>"<input v-model=\"model.name\" class=\"g-input\" placeholder=\"Name\">",
            ]
        ];
    }

}
