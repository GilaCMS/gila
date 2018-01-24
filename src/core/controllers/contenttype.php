<?php

/**
* Lists content types and shows grid content data
*/
class contenttype extends controller
{


    function __construct ()
    {
        $this->contenttype = self::contenttypeGen();
    }

    /**
    * Lists all the registered content types
    * @see gila::content()
    */
    function indexAction ()
    {
        view::set('contenttype',self::contenttypeGen());
        view::renderAdmin('admin/contenttype.php');
    }

    /**
    * Diplays content data for editing
    */
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
