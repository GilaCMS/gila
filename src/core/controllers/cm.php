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
        if(!$pnk->can('read')) return;
        include 'src/'.gila::$content[$this->contenttype];
        echo json_encode($table,JSON_PRETTY_PRINT);
    }

    /**
    * Lists registries of content type
    */
    function listAction ()
    {
        global $db;
        $pnk = new pnkTable('src/'.gila::$content[router::get("t",1)]);
        $result = [];
        if(!$pnk->can('read')) return;

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
        if(isset($_GET['groupby'])&&$_GET['groupby']!=null) {
            $this->group_rowsAction();
            return;
        }
        $pnk = new pnkTable('src/'.gila::$content[router::get("t",1)]);
        $result = [];
        if(!$pnk->can('read')) return;

        $result['fields'] = $pnk->fields();
        $res = $db->query("SELECT {$pnk->select()} FROM {$pnk->name()}{$pnk->where($_GET)}{$pnk->orderby()}{$pnk->limit()};");
        while($r = mysqli_fetch_row($res)) {
            $result['rows'][] = $r;
        }
        $result['startIndex'] = $pnk->startIndex();
        $result['totalRows'] = $db->value("SELECT COUNT(*) FROM {$pnk->name()}{$pnk->where($_GET)};");
        echo json_encode($result,JSON_PRETTY_PRINT);
    }

    function group_rowsAction ()
    {
        global $db;
        $pnk = new pnkTable('src/'.gila::$content[router::get("t",1)]);
        $result = [];
        $groupby = $_GET['groupby'];
        if(!$pnk->can('read')) return;

        $result['fields'] = $pnk->fields();
        $res = $db->query("SELECT {$pnk->selectsum($groupby)} FROM {$pnk->name()}{$pnk->where($_GET)}{$pnk->groupby($groupby)}{$pnk->orderby()};");
        while($r = mysqli_fetch_row($res)) {
            $result['rows'][] = $r;
        }
        $result['startIndex'] = $pnk->startIndex();
        $result['totalRows'] = $db->value("SELECT COUNT(*) FROM {$pnk->name()}{$pnk->where($_GET)}{$pnk->groupby($groupby)};");
        echo json_encode($result,JSON_PRETTY_PRINT);
    }

    /**
    * Updates registries of content type
    */
    function update_rowsAction ()
    {
        global $db;
        $result = [];
        $pnk = new pnkTable('src/'.gila::$content[router::get("t",1)]);

        if(isset($_GET['id'])&&$_GET['id']!='') {
            $id = $_GET['id'];
        } else if($pnk->can('create')){
            $res = $db->query("INSERT INTO {$pnk->name()}() VALUES();");
            $id = $db->insert_id;
        } else return;

        $res = $db->query("UPDATE {$pnk->name()}{$pnk->set($_POST)} WHERE {$pnk->id()}=?;",$id);
        $result['fields'] = $pnk->fields();
        $res = $db->query("SELECT {$pnk->select()} FROM {$pnk->name()} WHERE {$pnk->id()}=?;",$id);
        while($r = mysqli_fetch_row($res)) {
            $result['rows'][] = $r;
        }
        echo json_encode($result,JSON_PRETTY_PRINT);
    }

    /**
    * Insert new registry of content type
    */
    function insert_rowAction ()
    {
        global $db;
        $result = [];
        $pnk = new pnkTable('src/'.gila::$content[router::get("t",1)]);
        if(isset($_GET['id'])) {
            $fields=implode(',',$pnk->fields());
            $res = $db->query("INSERT INTO {$pnk->name()}($fields) SELECT $fields FROM {$pnk->name()} WHERE {$pnk->id()}=?;",$_GET['id']);
            $id = $_GET['id'];
        } else {
            $res = $db->query("INSERT INTO {$pnk->name()}() VALUES();");
            $id = $db->insert_id;
        }

        $result['fields'] = $pnk->fields();
        $res = $db->query("SELECT {$pnk->select()} FROM {$pnk->name()} WHERE {$pnk->id()}=?;",$id);
        while($r = mysqli_fetch_row($res)) {
            foreach($r as &$el) if($el==null) $el='';
            $result['rows'][] = $r;
        }
        echo json_encode($result,JSON_PRETTY_PRINT);
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
        } else {
            echo "User cannot delete";
        }
    }

}
