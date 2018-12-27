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
        $this->permissions = user::permissions(session::user_id());
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
        $pnk = new gTable(router::get("t",1));
        if(!$pnk->can('read')) return;
        $table = $pnk->getTable();
        foreach($table['fields'] as &$field) {
            unset($field['qtype']);
            unset($field['qoptions']);
            unset($field['qcolumn']);
        }
        echo json_encode($table,JSON_PRETTY_PRINT);
    }

    /**
    * Lists registries of content type
    */
    function listAction ()
    {
        global $db;
        $pnk = new gTable(router::get("t",1),$this->permissions);
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
        $pnk = new gTable(router::get("t",1),$this->permissions);
        $result = [];
        if(!$pnk->can('read')) return;

        $fieldlist = isset($_GET['id'])?'edit':'list';
        $result['fields'] = $pnk->fields($fieldlist);
        $result['rows'] = [];
        
        $ql = "SELECT {$pnk->select($result['fields'])} FROM {$pnk->name()}{$pnk->where($_GET)}{$pnk->orderby()}{$pnk->limit()};";
        $res = $db->query($ql);
        while($r = mysqli_fetch_row($res)) {
            $result['rows'][] = $r;
        }
        $result['startIndex'] = $pnk->startIndex();
        $result['totalRows'] = $db->value("SELECT COUNT(*) FROM {$pnk->name()}{$pnk->where($_GET)};");
        echo json_encode($result,JSON_PRETTY_PRINT);
    }

    function csvAction ()
    {
        global $db;
        $pnk = new gTable(router::get("t",1),$this->permissions);
        $result = [];
        if(!$pnk->can('read')) return;

        // filename to be downloaded
        $filename = router::get("t",1). date('Y-m-d') . ".csv";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $fields = $pnk->fields('csv');
        echo implode(',',$fields)."\n";
        $ql = "SELECT {$pnk->select($fields)} FROM {$pnk->name()}{$pnk->where($_GET)}{$pnk->orderby()}{$pnk->limit()};";
        $res = $db->query($ql);
        while($r = mysqli_fetch_row($res)) {
            foreach($r as &$str) {
                $str = preg_replace("/\t/", "", $str);//\\t
                $str = preg_replace("/\r?\n/", "", $str);//\\n
                if($str=="null") $str="";
                if(strstr($str, '"') || strstr($str, ',')) $str = '"' . str_replace('"', '""', $str) . '"';
            }
            echo implode(',',$r)."\n";
        }
    }

    function upload_csvAction ()
    {
        global $db;
        $inserted = 0;
        $updated = 0;
        $pnk = new gTable(router::get("t",1),$this->permissions);
        $filename = $_FILES["file"]["tmp_name"];        
        $fields = $pnk->fields('edit');
        $quest = "";
        $nfields = count($fields);
        $fields = implode(',',$fields);
        for($i=0; $i<$nfields; $i++) {
            if($i>0) $quest .= ',';
            $quest .= '?';
        }
        if(@$_FILES["file"]["size"] > 0) {
              $file = fopen($filename, "r");
            while (($row = fgetcsv($file, 10000, ",")) !== FALSE) {
               $ql = "INSERT INTO {$pnk->name()} ($fields) VALUES($quest);";
                if($db->query($ql, $row)) {
                    $inserted++;
                }
                else {
                    //try something else
                }
             }
             fclose($file);
         }
    }


    function group_rowsAction ()
    {
        global $db;
        $pnk = new gTable(router::get("t",1));
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
        $pnk = new gTable(router::get("t",1));

        if(isset($_GET['id'])&&$_GET['id']!='') {
            $id = $_GET['id'];
        } else if($pnk->can('create')){
            $res = $db->query("INSERT INTO {$pnk->name()}() VALUES();");
            $id = $db->insert_id;
        } else return;

        $pnk->updateMeta($id);
        $pnk->updateJoins($id);

        $res = $db->query("UPDATE {$pnk->name()}{$pnk->set($_POST)} WHERE {$pnk->id()}=?;",$id);
        if($db->error()) echo $db->error();
        $result['fields'] = $pnk->fields();
        $res = $db->query("SELECT {$pnk->select()} FROM {$pnk->name()} WHERE {$pnk->id()}=?;",$id);

        while($r = mysqli_fetch_row($res)) {
            $result['rows'][] = $r;
        }
        gila::setMt($pnk->name());
        echo json_encode($result,JSON_PRETTY_PRINT);
    }

    function empty_rowAction ()
    {
        $pnk = new gTable(router::get("t",1));
        $result['fields'] = $pnk->fields('create');
        $result['rows'][0] = $pnk->getEmpty();
        echo json_encode($result,JSON_PRETTY_PRINT);
    }

    /**
    * Insert new registry of content type
    */
    function insert_rowAction ()
    {
        global $db;
        $result = [];
        $pnk = new gTable(router::get("t",1));
        if(isset($_GET['id'])) {
            $fields = $pnk->fields('clone');
            $fields =  implode(',', $fields );
            $q = "INSERT INTO {$pnk->name()}($fields) SELECT $fields FROM {$pnk->name()} WHERE {$pnk->id()}=?;";
            $res = $db->query($q,$_GET['id']);
            $id = $db->insert_id;
        } else {
            $res = $db->query("INSERT INTO {$pnk->name()}() VALUES();");
            echo "INSERT INTO {$pnk->name()}() VALUES();";
            $id = $db->insert_id;
            if($id==0) {
                echo "Row was not created. Does this table exist?";
                exit;
            } else {
                $q = "UPDATE {$pnk->name()} {$pnk->set($_GET)} WHERE {$pnk->id()}=?;";
                $db->query($q,$id);
            }
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
        $pnk = new gTable(router::get("t",1), $this->permissions);
        if($pnk->can('delete')) {
            $res = $db->query("DELETE FROM {$pnk->name()} WHERE {$pnk->id()}=?;",$_POST['id']);
            echo $_POST['id'];
        } else {
            echo "User cannot delete";
        }
    }

}
