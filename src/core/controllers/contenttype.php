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
        //global $c;
        $this->hola2 = "estwththt";
        //view::set('contenttype',self::contenttypeGen());
        //$this->contenttype = self::contenttypeGen();

        view::renderAdmin('admin/contenttype.php');

    }

    function editAction ()
    {
        view::set('table',router::get('table',1));
        view::renderAdmin('admin/contenttype-edit.php');
    }

    function contenttypeGen()
    {
        foreach (gila::config('packages') as $pack) {
            $fol = 'src/'.$pack.'/tables/';
            if (file_exists($fol)) {
                $tables = @scandir($fol);
                if(is_array($tables)) foreach ($tables as $table) if ($table[0]!='.'){
                    yield explode('.',$table)[0];
                }
            }
        }
    }

}
