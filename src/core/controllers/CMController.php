<?php
use Gila\User;
use Gila\Config;
use Gila\Form;
use Gila\Session;
use Gila\Router;
use Gila\Table;
use Gila\Response;

/**
* Lists content types and shows grid content data
*/
class CMController extends Gila\Controller
{
  private $table;
  private $permissions;

  public function __construct()
  {
    @header('X-Robots-Tag: noindex, noffolow', true);
    $this->permissions = Session::permissions();
    $this->table = Router::param("t", 1);
    if (!Table::exist($this->table)) {
      http_response_code(404);
      exit;
    }
  }

  /**
  * Lists all the registered content types
  * @see Config::content()
  */
  public function indexAction()
  {
    header('Content-Type: application/json');
    $post = $_POST;
    $return = [];
    foreach ($post as $k=>$query) {
      $action = $query['action'];
      if ($action == 'list') {
        $return[$k] = self::list($query['table'], $query['filters'], $query);
      }
      if ($action == 'list_rows') {
        $return[$k] = self::list_rows($query['table'], $query['filters'], $query);
      }
      if ($action == 'describe') {
        $return[$k] = self::describe($query['table']);
      }
    }
    echo json_encode($return, JSON_PRETTY_PRINT);
  }

  /**
  * Displays info for content type
  */
  public function describeAction()
  {
    header('Content-Type: application/json');
    echo json_encode(self::describe($this->table), JSON_PRETTY_PRINT);
  }

  public function describe($table)
  {
    $gtable = new Table($table, $this->permissions);
    if (!$gtable->can('read')) {
      return;
    }
    $table = $gtable->getTable();
    foreach ($table['fields'] as &$field) {
      unset($field['qtype']);
      unset($field['qoptions']);
      unset($field['qcolumn']);
    }
    return $table;
  }


  /**
  * Lists registries of content type
  */
  public function listAction()
  {
    header('Content-Type: application/json');
    echo json_encode(self::list($this->table, $_GET, $_GET), JSON_PRETTY_PRINT);
  }

  public function list($table, $filters, $args)
  {
    $gtable = new Table($table, $this->permissions);
    if (!$gtable->can('read')) {
      return;
    }
    $res = $gtable->getRows($filters, $args);
    return $res;
  }

  public function getAction()
  {
    header('Content-Type: application/json');
    $table = new Table($this->table, $this->permissions);
    if (!$table->can('read')) {
      return;
    }
    if ($id = Router::param("id", 2)) {
      $filter = [$table->id()=>$id];
      $row = $table->getRow($filter);
    } else {
      $row = $table->getRow($_GET, $_GET);
    }
    foreach ($table->getTable()['children'] as $key=>$child) {
      $table = new Table($key);
      $filter = [$child['parent_id']=>$id];
      $row[$key] = $table->getRows($filter);
    }
    echo json_encode($row, JSON_PRETTY_PRINT);
  }

  public function list_rowsAction()
  {
    header('Content-Type: application/json');
    $result = self::list_rows($this->table, $_GET, $_GET);
    echo json_encode($result, JSON_PRETTY_PRINT);
  }

  public function list_rows($table, $filters, $args)
  {
    if (isset($args['groupby'])&&$args['groupby']!=null) {
      $this->group_rowsAction();
      return;
    }
    $gtable = new Table($table, $this->permissions);
    if (!$gtable->can('read')) {
      return;
    }
    $result = [];

    $fieldlist = isset($args['id']) ? 'edit' : 'list';
    $result['fields'] = $gtable->fields($fieldlist);
    $result['rows'] = [];
    $res = $gtable->getRows($filters, array_merge($args, ['select'=>$result['fields']]));

    foreach ($gtable->getFields($fieldlist) as $key=>$field) {
      if (isset($field['parseInt'])) {
        foreach ($res as &$r) {
          $r[$key] = (int)$r[$key];
        }
      }
    }
    foreach ($res as $r) {
      $result['rows'][] = array_values($r);
    }
    if ($error = Table::$error) {
      $result['error'] = $error;
    }
    $result['startIndex'] = $gtable->startIndex($args);
    $result['totalRows'] = $gtable->totalRows($filters);
    return $result;
  }

  public function csvAction()
  {
    global $db;
    $gtable = new Table($this->table, $this->permissions);
    $orderby = Router::request('orderby', []);
    if (!$gtable->can('read')) {
      return;
    }

    // filename to be downloaded
    $filename = $this->table. date('Y-m-d') . ".csv";
    header("Content-Disposition: attachment; filename=\"$filename\"");
    $fields = $gtable->fields('csv');
    echo implode(',', $fields)."\n";
    $ql = "SELECT {$gtable->select($fields)}
      FROM {$gtable->name()}{$gtable->where($_GET)}{$gtable->orderby($orderby)};";
    $res = $db->query($ql);
    while ($r = mysqli_fetch_row($res)) {
      foreach ($r as &$str) {
        $str = preg_replace("/\t/", "", $str);
        $str = preg_replace("/\r?\n/", "", $str);
        if ($str=="null") {
          $str="";
        }
        if (!is_numeric($str)) {
          if ($str[0]=='='||$str[0]=='-'||$str[0]=='+'||$str[0]=='@') {
            $str = '\''.$str;
          }
          $str = '"' . strtr($str, ['"'=>'""']) . '"';
        }
      }
      echo implode(',', $r)."\n";
    }
  }

  public function get_empty_csvAction()
  {
    $gtable = new Table($this->table, $this->permissions);
    if (!$gtable->can('create')) {
      return;
    }
    $filename = $this->table . "-example.csv";
    header("Content-Disposition: attachment; filename=\"$filename\"");
    $fields = $gtable->fields('upload_csv');
    echo implode(',', $fields);
  }

  public function upload_csvAction()
  {
    global $db;
    $gtable = new Table($this->table, $this->permissions);
    if (!$gtable->can('create')) {
      return;
    }
    $lines = 0;
    $filename = $_FILES["file"]["tmp_name"];
    $fields = $gtable->fields('upload_csv');
    $nfields = count($fields);
    $idField = $gtable->id();
    $inserted = 0;
    if (@$_FILES["file"]["size"] > 0) {
      $file = fopen($filename, "r");
      while (($row = fgetcsv($file, 10000, ",")) !== false) {
        $lines++;
        if ($lines==1) {
          $fieldIndex = array_flip($row);
          $columns = $row;
          continue;
        }
        if (count($row)<$nfields) {
          continue;
        }
        $values = [];
        $rowFields = [];
        foreach ($columns as $key) {
          if ($key!=$idField || !empty($row[$fieldIndex[$key]])) {
            $rowFields[] = $key;
            $values[] = $row[$fieldIndex[$key]];
            $data[$key] = $row[$fieldIndex[$key]];
          }
        }
        if (empty($row[$fieldIndex[$idField]])) {
          if ($_field = $gtable->getTable()['copy_from_src'] && !empty($data[$_field])) {
            $src = $data[$_field];
            $pathinfo = pathinfo($src);
            $ext = strtolower($pathinfo['extension']) ?? 'png';
            if (!in_array($ext, ['jpg','jpeg','webp','png'])) {
              break;
            }
            $file = 'assets/uploads/'.time().'.'.$ext;
            copy($src, $file);
            $data[$_field] = $file;
          }
          $id = $gtable->createRow($data);
          echo $id.',';
        } else {
          $id = $row[$fieldIndex[$idField]];
          $set = $gtable->set($data);
          if ($error = Table::$error) {
            die('{"success":false, "error":"'.$error.'"}');
          }
          $db->query("UPDATE {$gtable->name()}{$set} WHERE {$gtable->id()}=?;", $id);
          if ($db->error()) {
            die('{"success":false, "error":"'.$db->error().'"}');
          }
        }
        if ($id) {
          $gtable->updateMeta($id, $data);
          $gtable->updateJoins($id, $data);
        }
      }
      fclose($file);
    }
    Response::success();
  }

  public function group_rowsAction()
  {
    global $db;
    header('Content-Type: application/json');
    $gtable = new Table($this->table, $this->permissions);
    if (!$gtable->can('read')) {
      return;
    }
    $result = [];
    $groupby = Router::request('groupby');
    $orderby = Router::request('orderby', []);
    $counter = isset($_GET['counter']) ? ',COUNT(*) AS '.$_GET['counter'] : '';

    $result['fields'] = $gtable->fields();
    $res = $db->query("SELECT {$gtable->selectsum($groupby)}$counter FROM {$gtable->name()}
      {$gtable->where($_GET)}{$gtable->groupby($groupby)}{$gtable->orderby($orderby)};");
    while ($r = mysqli_fetch_row($res)) {
      $result['rows'][] = $r;
    }
    echo json_encode($result, JSON_PRETTY_PRINT);
  }

  /**
  * Updates registries of content type
  */
  public function update_rowsAction()
  {
    global $db;
    header('Content-Type: application/json');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      return;
    }
    $gtable = new Table($this->table, $this->permissions);

    if (isset($_GET['id']) && $_GET['id']>0 && $gtable->can('update')) {
      $id = $_GET['id'];
    } else {
      $id = $gtable->createRow($_POST);
      if ($id===0) {
        Response::error(Table::$error ?? '', 200);
      }
    }

    $result = [];
    $ids = explode(',', (string)$id);
    $result['ids'] = $ids;
    $result['fields'] = $gtable->fields();

    foreach ($ids as $id) {
      $data = $_POST;
      if (isset($_GET['id']) && $_GET['id']>0) {
        $gtable->event('update', $data);
      }
      $gtable->updateMeta($id);
      $gtable->updateJoins($id);
      $set = $gtable->set($data);
      if ($error = Table::$error) {
        Response::error($error, 200);
      }
      $res = DB::query("UPDATE {$gtable->name()}{$set} WHERE {$gtable->id()}=?;", $id);
      if ($db->error()) {
        $result['error'][] = $db->error();
      }
      $q = "SELECT {$gtable->select()} FROM {$gtable->name()} WHERE {$gtable->id()}=?;";
      $result['rows'] = DB::get($q, [$id]);  
    }

    Config::setMt($gtable->name());
    echo json_encode($result);
  }

  public function empty_rowAction()
  {
    header('Content-Type: application/json');
    $gtable = new Table($this->table, $this->permissions);
    $result['fields'] = $gtable->fields('create');
    $result['rows'][0] = $gtable->getEmpty();
    echo json_encode($result, JSON_PRETTY_PRINT);
  }

  /**
  * Insert new registry of content type
  */
  public function insert_rowAction()
  {
    global $db;
    header('Content-Type: application/json');
    $gtable = new Table($this->table, $this->permissions);
    if (!$gtable->can('create')) {
      return;
    }
    $result = [];
    $data = $_POST;

    if (isset($_POST['id'])) {
      $gtable->event('create', $data);
      $fields = $gtable->fields('clone');
      $metaFields = [];
      if (($idkey = array_search($gtable->id(), $fields)) !== false) {
        unset($fields[$idkey]);
      }
      foreach ($fields as $key=>$field) {
        if ($gtable->fieldAttr($field, 'meta_key')) {
          unset($fields[$key]);
          $metaFields[] = $field;
        }
        if ($gtable->fieldAttr($field, 'join_table')) {
          unset($fields[$key]);
          $joinFields[] = $field;
        }
      }
      $fieldStr =  implode(',', $fields);
      $q = "INSERT INTO {$gtable->name()}($fieldStr) SELECT $fieldStr FROM {$gtable->name()} x WHERE {$gtable->id()}=?;";
      $res = $db->getAssoc("SELECT $fieldStr FROM {$gtable->name()} WHERE {$gtable->id()}=?;", $_POST['id'])[0];
      $id = $gtable->createRow($res);
      if ($id===0) {
        Response::error('Row could not be created');
        exit;
      }

      foreach ($metaFields as $field) {
        list($mt, $vt) = $gtable->getMT($field);
        $q = "INSERT INTO {$mt[0]}({$mt[1]},{$mt[2]},{$mt[3]}) SELECT $id,{$mt[2]},{$mt[3]}
        FROM {$mt[0]} x WHERE {$mt[1]}=? AND {$mt[2]}=?;";
        $res = $db->query($q, [$_POST['id'], $vt]);
      }
      foreach ($joinFields as $field) {
        list($jtable, $this_id, $other_id) = $gtable->getTable()['fields'][$field]['join_table'];
        $q = "INSERT INTO {$jtable}({$this_id},{$other_id}) SELECT $id,{$other_id}
        FROM {$jtable} x WHERE {$this_id}=?;";
        $res = $db->query($q, [$_POST['id']]);
      }
    } else {
      $id = $gtable->createRow($data);
      if ($id===0) {
        Response::error("Row was not created. Does this table exist?");
        exit;
      } else {
        $q = "UPDATE {$gtable->name()} {$gtable->set($data)} WHERE {$gtable->id()}=?;";
        $db->query($q, $id);
      }
    }

    $result['fields'] = $gtable->fields();
    $res = $db->query("SELECT {$gtable->select()} FROM {$gtable->name()} WHERE {$gtable->id()}=?;", $id);
    while ($r = mysqli_fetch_row($res)) {
      foreach ($r as &$el) {
        if ($el==null) {
          $el='';
        }
      }
      $result['rows'][] = $r;
    }
    echo json_encode($result, JSON_PRETTY_PRINT);
  }

  /**
  * Delete registry of content type
  */
  public function deleteAction()
  {
    header('Content-Type: application/json');
    $gtable = new Table($this->table, $this->permissions);
    if ($gtable->can('delete')) {
      $ids = explode(',', $_POST['id']);
      foreach ($ids as $id) {
        $gtable->deleteRow($id);
      }
      $response = '{"id":"'.$_POST['id'].'"}';
    } else {
      http_response_code(403);
      $response = '{"error":"User cannot delete"}';
    }
    echo $response;
  }

  public function edit_formAction()
  {
    global $db;
    $t = htmlentities($this->table);
    $gtable = new Table($t, $this->permissions);
    if (!$gtable->can('update')) {
      return;
    }
    $callback = Router::param("callback") ?? $t.'_action';
    $html = '';
    $id = Router::param("id", 2);
    $id = (int)$id;

    if ($id>0) {
      $fields = $gtable->fields('edit');
      $ql = "SELECT {$gtable->select($fields)} FROM {$gtable->name()}{$gtable->where($_GET)};";
      $res = $db->get($ql)[0];
      $getFields = $gtable->getFields('edit');
      $values = $res;
    } else {
      $getFields = $gtable->getFields('create');
      $values = $values;
    }
    $fieldValues = [];
    foreach ($getFields as $key=>$field) {
      if (isset($field['type']) && $field['type']=='meta') {
        $fieldValues[$key] = explode(',', $values[$key]);
      }
    }
    $html .= '<form id="'.$t.'-edit-item-form" data-table="'.$t.'" data-id="'.$id.'" class="g-form"';
    $html .= ' action="javascript:'.$callback.'()" data-values=\''.htmlentities(json_encode($fieldValues, JSON_UNESCAPED_UNICODE)).'\'>';
    $html .= '<button style="position:absolute;top:-1000px"></button>';
    $html .= '<div class="edit-item-form">';
    $html .= Form::hiddenInput();
    $html .= Form::html($getFields, $values);

    $child_id = '<span id="edit_popup_child"></span>';
    foreach ($gtable->getTable()['children']??[] as $ckey=>$child) {
      $html .= $child_id;
      $child_id = "";
      $html .= '<g-table v-if="id>0" gtype="'.$ckey.'" gchild=1 ';
      $html .= 'gtable="'.htmlentities(json_encode($child['table'])).'" ';
      $html .= 'gfields="'.htmlentities(json_encode($child['list'])).'" ';
      $html .= ':gfilters="\'&amp;'.$child['parent_id'].'=\'+id">';
      $html .= '</g-table>';
    }

    $html .= '</div></form>';
    echo $html;
  }

  public function edit_form2Action()
  {
    $this->edit_formAction();
  }

  public function select_rowAction()
  {
    $t = htmlentities($this->table);
    $gtable = new Table($t, $this->permissions);
    if (!$gtable->can('read')) {
      return;
    }

    echo '<div id="gtable_select_row"><g-table ';
    echo 'gtable="'.htmlentities(json_encode($gtable->getTable())).'" ';
    echo 'gfields="'.htmlentities(json_encode($gtable->fields('list'))).'" ';
    echo 'gtype="'.$this->table.'">';
    echo '</g-table>';
    echo '</div>';
  }

  public function open_tableAction()
  {
    $t = htmlentities($this->table);
    $gtable = new Table($t, $this->permissions);
    if (!$gtable->can('read')) {
      return;
    }

    echo '<div id="gtable_open_table"><g-table ';
    echo 'gtable="'.htmlentities(json_encode($gtable->getTable())).'" ';
    echo 'gfields="'.htmlentities(json_encode($gtable->fields('list'))).'" ';
    if (isset($_POST)) {
      echo 'gfilters="&'.htmlentities(http_build_query($_POST)).'" ';
    }
    echo 'gtype="'.$this->table.'">';
    echo '</g-table>';
    echo '</div>';
  }
}
