<?php
use core\models\user as user;

/**
* Lists content types and shows grid content data
*/
class cm extends controller
{
    private $contenttype;
    private $table;
    private $permissions;

    function __construct ()
    {
        $this->permissions = user::metaList( session::user_id(), 'privilege');
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
    * Displays info for content type
    */
    function describeAction ()
    {
        global $table;
        $this->contenttype = router::get("t",1);
        include 'src/'.gila::$content[$this->contenttype];
        echo '<pre>'.json_encode($table,JSON_PRETTY_PRINT).'</pre>';
    }

    /**
    * Lists registries of content type
    */
    function listAction ()
    {
        global $db;
        $pnk = new pnkTable('src/'.gila::$content[router::get("t",1)]);
        $result = [];

        $res = $db->query("SELECT {$pnk->select()} FROM {$pnk->name()}{$pnk->where($_GET)}{$pnk->orderby()};");
        while($r = mysqli_fetch_assoc($res)) {
            /*if(isset($_GET['$sub'])) {
                if($r[$_GET['$sub']] != null) {
                    $res2 = $db->query("SELECT * FROM postcategory WHERE id IN({$r[$_GET['$sub']]});");
                    $r[$_GET['$sub']]=[];
                    while($r2 = mysqli_fetch_assoc($res2)) {
                        $r[$_GET['$sub']][] = $r2;
                    }
                } else {
                    $r[$_GET['$sub']]=[];
                }
            }*/
            $result[] = $r;
        }
        echo json_encode($result,JSON_PRETTY_PRINT);
    }

    function list_rowsAction ()
    {
        global $db;
        $pnk = new pnkTable('src/'.gila::$content[router::get("t",1)]);
        $result = [];
        $result['fields'] = $pnk->fields();
        //echo "SELECT {$pnk->select()} FROM {$pnk->name()}{$pnk->where($_GET)}{$pnk->orderby()}{$pnk->limit()};";
        $res = $db->query("SELECT {$pnk->select()} FROM {$pnk->name()}{$pnk->where($_GET)}{$pnk->orderby()}{$pnk->limit()};");
        while($r = mysqli_fetch_row($res)) {
            $result['rows'][] = $r;
        }
        $result['startIndex'] = $pnk->startIndex();
        $result['totalRows'] = $db->value("SELECT COUNT(*) FROM {$pnk->name()}{$pnk->where($_GET)};");
        echo json_encode($result,JSON_PRETTY_PRINT);
    }

    /**
    * Updates registries of content type
    */
    function updateAction ()
    {
        global $db;
        $result = [];
        $pnk = new pnkTable('src/'.gila::$content[router::get("t",1)]);

        $res = $db->query("UPDATE {$pnk->name()}{$pnk->set()}{$this->where()};");
    }

    /**
    * Updates registries of content type
    */
    function insertAction ()
    {
        global $db;
        $result = [];
        $pnk = new pnkTable('src/'.gila::$content[router::get("t",1)]);
        $res = $db->query("INSERT INTO {$pnk->name()}() VALUES();");
        $result['id'] = $db->insert_id;
        echo '<pre>'.json_encode($result,JSON_PRETTY_PRINT).'</pre>';
    }

    /**
    * Delete registry of content type
    */
    function deleteAction ()
    {
        global $db;
        $pnk = new pnkTable('src/'.gila::$content[router::get("t",1)], $this->permissions);
        if($pnk->can('delete')) {
            $res = $db->query("DELETE FROM {$pnk->name()} WHERE {$pnk->id()}=?;",$_POST['id']);
            echo $_POST['id'];
        }
    }

}
