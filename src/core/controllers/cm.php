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
    header('Content-Type: application/json');
  }

  /**
  * Lists all the registered content types
  * @see gila::content()
  */
  function indexAction ()
  {
    $post = $_POST;
    $return = [];
    foreach($post as $k=>$query){
      $action = $query['action'];
      if($action == 'list') {
        $return[$k] = self::list($query['filters'], $query);
      }
    }
    echo json_encode($return,JSON_PRETTY_PRINT);
  }

  /**
  * Displays info for content type
  */
  function describeAction ()
  {
    $pnk = new gTable(router::get("t",1), $this->permissions);
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
    echo json_encode(self::list($_GET, $_GET), JSON_PRETTY_PRINT);
  }

  function list ($filters, $args)
  {
    $gtable = new gTable(router::get("t",1), $this->permissions);
    if(!$gtable->can('read')) return;
    $res = $gtable->getRows($filters, $args);
    return $res;
  }

  function getAction ()
  {
    $table = new gTable(router::get("t",1), $this->permissions);
    if(!$table->can('read')) return;
    if($id = router::get("id",2)) {
      $filter = [$table->id()=>$id];
      $row = $table->getRow($filter);
    } else {
      $row = $table->getRow($_GET, $_GET);
    }
    foreach ($table->getTable()['children'] as $key=>$child) {
      $table = new gTable($key);
      $filter = [$child['parent_id']=>$id];
      $row[$key] = $table->getRows($filter);
    }
    echo json_encode($row, JSON_PRETTY_PRINT);
  }

  function list_rowsAction ()
  {
    if(isset($_GET['groupby'])&&$_GET['groupby']!=null) {
      $this->group_rowsAction();
      return;
    }
    $pnk = new gTable(router::get("t",1), $this->permissions);
    if(!$pnk->can('read')) return;
    $result = [];

    $fieldlist = isset($_GET['id'])?'edit':'list';
    $result['fields'] = $pnk->fields($fieldlist);
    $result['rows'] = [];
    $res = $pnk->getRows($_GET, array_merge($_GET, ['select'=>$result['fields']]));
    foreach($res as $r) $result['rows'][] = array_values($r);
    $result['startIndex'] = $pnk->startIndex();
    $result['totalRows'] = $pnk->totalRows($_GET);
    echo json_encode($result, JSON_PRETTY_PRINT);
  }

  function csvAction ()
  {
    global $db;
    $pnk = new gTable(router::get("t",1), $this->permissions);
    if(!$pnk->can('read')) return;
    $result = [];

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
    $pnk = new gTable(router::get("t",1), $this->permissions);
    if(!$pnk->can('create')) return;
    $inserted = 0;
    $updated = 0;
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
    $pnk = new gTable(router::get("t",1), $this->permissions);
    if(!$pnk->can('read')) return;
    $result = [];
    $groupby = $_GET['groupby'];
    $counter = isset($_GET['counter']) ? ',COUNT(*) AS '.$_GET['counter'] : '';

    $result['fields'] = $pnk->fields();
    $res = $db->query("SELECT {$pnk->selectsum($groupby)}$counter FROM {$pnk->name()}
      {$pnk->where($_GET)}{$pnk->groupby($groupby)}{$pnk->orderby()};");
    while($r = mysqli_fetch_row($res)) {
      $result['rows'][] = $r;
    }
    echo json_encode($result,JSON_PRETTY_PRINT);
  }

  /**
  * Updates registries of content type
  */
  function update_rowsAction ()
  {
    global $db;
    $pnk = new gTable(router::get("t",1), $this->permissions);

    if(isset($_GET['id']) && $_GET['id']!='' && $pnk->can('update')) {
      $id = $_GET['id'];
    } else if($pnk->can('create')) {
      $res = $db->query("INSERT INTO {$pnk->name()}() VALUES();");
      $id = $db->insert_id;
    } else return;

    $result = [];
    $ids = explode(',',$id);
    $result['fields'] = $pnk->fields();

    foreach($ids as $id) {
      $pnk->updateMeta($id);
      $pnk->updateJoins($id);
      $res = $db->query("UPDATE {$pnk->name()}{$pnk->set($_POST)} WHERE {$pnk->id()}=?;",$id);
      if($db->error()) @$result['error'][] = $db->error();
      $gen = $db->gen("SELECT {$pnk->select()} FROM {$pnk->name()} WHERE {$pnk->id()}=?;",$id);

      foreach($gen as $r) {
        @$result['rows'][] = $r;
      }
    }
    gila::setMt($pnk->name());
    echo json_encode($result,JSON_PRETTY_PRINT);
  }

  function empty_rowAction ()
  {
    $pnk = new gTable(router::get("t",1), $this->permissions);
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
    $pnk = new gTable(router::get("t",1), $this->permissions);
    if(!$pnk->can('create')) return;
    $result = [];

    if(isset($_GET['id'])) {
      $fields = $pnk->fields('clone');
      if (($idkey = array_search($pnk->id(), $fields)) !== false) {
        unset($fields[$idkey]);
      }
      $fields =  implode(',', $fields );
      $q = "INSERT INTO {$pnk->name()}($fields) SELECT $fields FROM {$pnk->name()} WHERE {$pnk->id()}=?;";
      $res = $db->query($q,$_GET['id']);
      $id = $db->insert_id;
    } else {
      $res = $db->query("INSERT INTO {$pnk->name()}() VALUES();");
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
    $gtable = new gTable(router::get("t",1), $this->permissions);
    if($gtable->can('delete')) {
      $gtable->deleteRow($_POST['id']);
      echo $_POST['id'];
    } else {
      echo "User cannot delete";
    }
  }

  function edit_formAction ()
  {
    global $db;
    $t = router::get("t",1);
    $pnk = new gTable($t, $this->permissions);
    if(!$pnk->can('update')) return;

    $fields = $pnk->fields('edit');
    $id = router::get("id",2);
    echo '<form id="'.$t.'-edit-item-form" data-table="'.$t.'" data-id="'.$id.'" class="g-form"><div>';
    if($id) {
      $w = ['id'=>$id];
      $ql = "SELECT {$pnk->select($fields)} FROM {$pnk->name()}{$pnk->where()};";
      $res = $db->get($ql)[0];
      echo gForm::html($pnk->getFields('edit'),$res);
    } else {
      echo gForm::html($pnk->getFields('edit'));
    }
    echo '</div></form>';
  }

}
