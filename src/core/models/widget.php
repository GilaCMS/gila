<?php
namespace core\models;

class widget
{

    function __construct ()
    {

    }

    static function getById($id)
    {
        global $db;
        return $db->get("SELECT * FROM widget WHERE id=?",$id)[0];
    }
}
