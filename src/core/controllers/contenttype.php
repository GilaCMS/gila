<?php


class contenttype extends controller
{
    public $hola2;

    function __construct ()
    {
        $this->contenttype = self::contenttypeGen();
        $this->hola = "estwththt";
        //$this->hola2 = "ttt";
    }

    function indexAction ()
    {
        // list here all the content types
        view::set('contenttype',self::contenttypeGen());
        view::renderAdmin('admin/contenttype.php');
    }

    function editAction ()
    {
        $table = router::get('table',1);
        if(isset(gila::$content[$table])) {
            view::set('table',gila::$content[$table]);
            view::renderAdmin('admin/contenttype-edit.php');
        }else {
            self::indexAction();
        }
    }

    function contenttypeGen()
    {
        foreach(gila::$content as $key=>$type)
            yield $key;
    }
}
