<?php
use core\models\User;

/**
* Lists content types and shows grid content data
*/
class cm extends Controller
{
  private $contenttype;
  private $table;
  private $permissions;

  function __construct ()
  {
    $this->permissions = User::permissions(Session::userId());
    $this->table = Router::get("t",1);
    if(!isset(Gila::$content[$this->table])) {
      http_response_code(404);
      exit;
    }
  }

  /**
  * Lists all the registered content types
  * @see Gila::content()
  */
  function indexAction ()
  {
    header('Content-Type: application/json');
    $post = $_POST;
    $return = [];
    foreach($post as $k=>$query){
      $action = $query['action'];
      if($action == 'list') {
        $return[$k] = self::list($query['table'], $query['filters'], $query);
      }
      if($action == 'list_rows') {
        $return[$k] = self::list_rows($query['table'], $query['filters'], $query);
      }
      if($action == 'describe') {
        $return[$k] = self::describe($query['table']);
      }
    }
    echo json_encode($return,JSON_PRETTY_PRINT);
  }

  /**
  * Displays info for content type
  */
  function describeAction ()
  {
    header('Content-Type: application/json');
    echo json_encode(self::describe($this->table), JSON_PRETTY_PRINT);
  }

  function describe ($table)
  {
    $pnk = new gTable($table, $this->permissions);
    if(!$pnk->can('read')) return;
    $table = $pnk->getTable();
    foreach($table['fields'] as &$field) {
      unset($field['qtype']);
      unset($field['qoptions']);
      unset($field['qcolumn']);
    }
    return $table;
  }


  /**
  * Lists registries of content type
  */
  function listAction ()
  {
    header('Content-Type: application/json');
    echo json_encode(self::list($this->table, $_GET, $_GET), JSON_PRETTY_PRINT);
  }

  function list ($table, $filters, $args)
  {
    $gtable = new gTable($table, $this->permissions);
    if(!$gtable->can('read')) return;
    $res = $gtable->getRows($filters, $args);
    return $res;
  }

  function getAction ()
  {
    header('Content-Type: application/json');
    $table = new gTable($this->table, $this->permissions);
    if(!$table->can('read')) return;
    if($id = Router::get("id",2)) {
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
    header('Content-Type: application/json');
    $result = self::list_rows($this->table, $_GET, $_GET);
    echo json_encode($result, JSON_PRETTY_PRINT);
  }

  function list_rows ($table, $filters, $args)
  {
    if(isset($args['groupby'])&&$args['groupby']!=null) {
      $this->group_rowsAction();
      return;
    }
    $pnk = new gTable($table, $this->permissions);
    if(!$pnk->can('read')) return;
    $result = [];

    $fieldlist = isset($args['id']) ? 'edit' : 'list';
    $result['fields'] = $pnk->fields($fieldlist);
    $result['rows'] = [];
    $res = $pnk->getRows($filters, array_merge($args, ['select'=>$result['fields']]));
    foreach($res as $r) $result['rows'][] = array_values($r);
    $result['startIndex'] = $pnk->startIndex($args);
    $result['totalRows'] = $pnk->totalRows($filters);
    return $result;
  }

  function csvAction ()
  {
    global $db;
    $pnk = new gTable($this->table, $this->permissions);
    $orderby = Router::request('orderby', []);
    if(!$pnk->can('read')) return;
    $result = [];

    // filename to be downloaded
    $filename = $this->table. date('Y-m-d') . ".csv";
    header("Content-Disposition: attachment; filename=\"$filename\"");
    $fields = $pnk->fields('csv');
    echo implode(',',$fields)."\n";
    $ql = "SELECT {$pnk->select($fields)}
      FROM {$pnk->name()}{$pnk->where($_GET)}{$pnk->orderby($orderby)};";
    $res = $db->query($ql);
    while($r = mysqli_fetch_row($res)) {
      foreach($r as &$str) {
        $str = preg_replace("/\t/", "", $str);//\\t
        $str = preg_replace("/\r?\n/", "", $str);//\\n
        if($str=="null") $str="";
        if(strstr($str, '"') || strstr($str, ',')) $str = '"' . strtr($str, ['"'=>'""']) . '"';
      }
      echo implode(',',$r)."\n";
    }
  }

  function get_empty_csvAction ()
  {
    $pnk = new gTable($this->table, $this->permissions);
    if(!$pnk->can('create')) return;
    $filename = $this->table . "-example.csv";
    header("Content-Disposition: attachment; filename=\"$filename\"");
    $fields = $pnk->fields('upload_csv');
    echo implode(',',$fields); 
  }

  function upload_csvAction ()
  {
    global $db;
    $pnk = new gTable($this->table, $this->permissions);
    if(!$pnk->can('create')) return;
    $lines = 0;
    $filename = $_FILES["file"]["tmp_name"];
    $fields = $pnk->fields('upload_csv');
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
        $lines++;
        if($lines==1) continue;
        if(count($row)<$nfields) continue;
        $ql = "REPLACE INTO {$pnk->name()} ($fields) VALUES($quest);";
        echo $ql.'\n';
        echo implode(',',$row);
        if($db->query($ql, $row)) {
          $inserted++;
        }
      }
      fclose($file);
    }
  }

  function group_rowsAction ()
  {
    global $db;
    header('Content-Type: application/json');
    $pnk = new gTable($this->table, $this->permissions);
    if(!$pnk->can('read')) return;
    $result = [];
    $groupby = Router::request('groupby');
    $orderby = Router::request('orderby', []);
    $counter = isset($_GET['counter']) ? ',COUNT(*) AS '.$_GET['counter'] : '';

    $result['fields'] = $pnk->fields();
    $res = $db->query("SELECT {$pnk->selectsum($groupby)}$counter FROM {$pnk->name()}
      {$pnk->where($_GET)}{$pnk->groupby($groupby)}{$pnk->orderby($orderby)};");
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
    header('Content-Type: application/json');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
    $pnk = new gTable($this->table, $this->permissions);

    if(isset($_GET['id']) && $_GET['id']!='' && $pnk->can('update')) {
      $id = $_GET['id'];
    } else if($pnk->can('create')) {
      $insert_fields = [];
      $insert_values = [];
      // add the filter values
      foreach($pnk->getTable()['fields'] as $field=>$value) {
        if (isset($_GET[$field])) {
          $insert_fields[]=$field;
          $insert_values[]=(int)$_GET[$field];
        }
      }
      $fnames = implode(',',$insert_fields);
      $values = implode(',',$insert_values);
      $q = "INSERT INTO {$pnk->name()}($fnames) VALUES($values);";
      $res = $db->query($q);
      $id = $db->insert_id;
    } else return;

    $result = [];
    $ids = explode(',',$id);
    $result['fields'] = $pnk->fields();

    foreach($ids as $id) {
      $data = $_POST;
      $pnk->event('update', $data);
      $pnk->updateMeta($id);
      $pnk->updateJoins($id);
      $res = $db->query("UPDATE {$pnk->name()}{$pnk->set($data)} WHERE {$pnk->id()}=?;",$id);
      if($db->error()) @$result['error'][] = $db->error();
      $gen = $db->gen("SELECT {$pnk->select()} FROM {$pnk->name()} WHERE {$pnk->id()}=?;",$id);

      foreach($gen as $r) {
        @$result['rows'][] = $r;
      }
    }
    Gila::setMt($pnk->name());
    echo json_encode($result,JSON_PRETTY_PRINT);
  }

  function empty_rowAction ()
  {
    header('Content-Type: application/json');
    $pnk = new gTable($this->table, $this->permissions);
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
    header('Content-Type: application/json');
    $pnk = new gTable($this->table, $this->permissions);
    if(!$pnk->can('create')) return;
    $result = [];
    $data = $_POST;

    $pnk->event('create', $data);
    if(isset($_REQUEST['id'])) {
      $fields = $pnk->fields('clone');
      if (($idkey = array_search($pnk->id(), $fields)) !== false) {
        unset($fields[$idkey]);
      }
      $fields =  implode(',', $fields );
      $q = "INSERT INTO {$pnk->name()}($fields) SELECT $fields FROM {$pnk->name()} WHERE {$pnk->id()}=?;";
      $res = $db->query($q, $_REQUEST['id']);
      $id = $db->insert_id;
    } else {
      $res = $db->query("INSERT INTO {$pnk->name()}() VALUES();");
      $id = $db->insert_id;
      if($id==0) {
        echo "Row was not created. Does this table exist?";
        exit;
      } else {
        $q = "UPDATE {$pnk->name()} {$pnk->set($data)} WHERE {$pnk->id()}=?;";
        $db->query($q, $id);
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
    header('Content-Type: application/json');
    $gtable = new gTable($this->table, $this->permissions);
    if($gtable->can('delete')) {
      $gtable->deleteRow($_POST['id']);
      $response = '{"id":"'.$_POST['id'].'"}';
    } else {
      http_response_code(403);
      $response = '{"error":"User cannot delete"}';
    }
    echo $response;
  }

  function edit_formAction ()
  {
    global $db;
    $t = htmlentities($this->table);
    $pnk = new gTable($t, $this->permissions);
    if(!$pnk->can('update')) return;

    $fields = $pnk->fields('edit');
    $id = Router::get("id",2);
    $id = (int)$id;
    echo '<form id="'.$t.'-edit-item-form" data-table="'.$t.'" data-id="'.$id.'" class="g-form"><div>';
    echo gForm::hiddenInput();
    if($id) {
      $w = ['id'=>$id];
      $ql = "SELECT {$pnk->select($fields)} FROM {$pnk->name()}{$pnk->where($_GET)};";
      $res = $db->get($ql)[0];
      echo gForm::html($pnk->getFields('edit'),$res);
    } else {
      echo gForm::html($pnk->getFields('edit'));
    }
    echo '</div></form>';
  }

}
