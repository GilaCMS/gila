<?php

namespace Gila;

class Table
{
  private $table;
  private $permissions;
  private $db;
  public static $error = null;
  public static $tableList = [];
  public static $basicOps = [
    'gt'=>'>', 'ge'=>'>=', 'lt'=>'<', 'le'=>'<='
  ];

  public function __construct($content, $permissions = ['admin'])
  {
    global $db;
    $this->db = &$db;
    $this->permissions = $permissions;
    
    if (isset(self::$tableList[$content])) {
      $this->table = self::$tableList[$content];
      return;
    }
    $this->loadSchema($content);

    if (isset(Config::$contentInit[$this->table['name']])) {
      foreach (@Config::$contentInit[$this->table['name']] as $init) {
        $init($this->table);
      }
    }
    if (isset($this->table['lang'])) {
      Config::addLang($this->table['lang']);
    }
    foreach ($this->table['fields'] as $key => &$field) {
      if (isset($field['qoptions'])) {
        if (!isset($field['options'])) {
          $field['options'] = [];
        }
        if (is_array($field['qoptions'])) {
          $o = $field['qoptions'];
          $optionTable = new Table($o[2]);
          $res = $optionTable->getRows($o[3]??[], ['select'=>[$o[0],$o[1]]]);
          foreach ($res as $r) {
            $field['options'][$r[$o[0]]] = $r[$o[1]];
          }
        } else {
          $field['options'] = $this->db->getOptions($field['qoptions']);
        }
      }
      if (isset($field['title'])) {
        $field['title'] = Config::tr($field['title']);
      } else {
        $field['title'] = ucfirst(Config::tr($key));
      }
    }
    if (isset($this->table['children'])) {
      foreach ($this->table['children'] as $key => &$child) {
        $child_table = new Table($key, $permissions);
        $child['table'] = $child_table->getTable();
      }
    }
    $this->table['title'] = Config::tr($this->table['title']??$this->table['name']);

    if (!isset($this->table['permissions'])) {
      $this->table['permissions'] = [];
    }
    $p = &$this->table['permissions'];
    if (!isset($p['create'])) {
      $p['create'] = ['admin'];
    }
    if (!isset($p['read'])) {
      $p['read'] = ['admin'];
    }
    if (!isset($p['update'])) {
      $p['update'] = ['admin'];
    }
    if (!isset($p['delete'])) {
      $p['delete'] = ['admin'];
    }

    self::$tableList[$content] = $this->table;
  }

  public static function exist($table)
  {
    global $db;
    if (isset(Config::$content[$table])) {
      return true;
    }
    if ($db->read()->value("SELECT id FROM tableschema WHERE `name`=?;", [$table])) {
      return true;
    }
    return false;
  }

  public function loadSchema($content)
  {
    global $db;

    if (isset(Config::$content[$content])) {
      $path = 'src/'.Config::$content[$content];
    } elseif (file_exists($content)) {
      $path = $content;
    } else {
      $path = 'src'.$content;
      if ($json = $db->read()->value("SELECT `data` FROM tableschema WHERE `name`=?;", [$content])) {
        $this->table = json_decode($json, true);
        $path = null;
      }
    }
    
    if ($path) {
      $this->table = include $path;
      if (isset($table)) {
        $this->table = $table;
      }
    }

    if ($ext = $this->table['extends']??null) {
      $baseTable = include 'src/'.$ext;
      $this->table = self::extend_recursive($baseTable, $this->table);
    }

    if ($user_id = $this->table['filter_owner']??null) {
      @$this->table['filters'][$user_id] = Session::userId();
      foreach (['search_boxes','csv','list','edit','create'] as $key) {
        if (isset($this->table[$key])) {
          $this->table[$key] = array_diff($this->table[$key], [$user_id]);
        }
        $this->table['fields'][$user_id][$key] = false;
      }
    }
  }

  public static function extend_recursive($table, $extTable)
  {
    foreach ($extTable as $key=>$el) {
      if (is_array($el) && !is_numeric($key)) {
        $table[$key] = self::extend_recursive($table[$key]??[], $el);
      } elseif (is_numeric($key)) {
        if (!in_array($el, $table)) {
          $table[] = $el;
        }
      } else {
        $table[$key] = $el;
      }
    }
    return $table;
  }

  public function name()
  {
    return $this->table['name'];
  }

  public function id()
  {
    return $this->table['id'] ?? 'id';
  }

  public function fieldAttr($field, $attr)
  {
    if (isset($this->table['fields'][$field])) {
      if (isset($this->table['fields'][$field][$attr])) {
        return $this->table['fields'][$field][$attr];
      }
    }
    return false;
  }

  public function fields($output = 'list')
  {
    if (!isset($this->table[$output])) {
      $this->table[$output]=[];
      foreach ($this->table['fields'] as $k=>$f) {
        if (!isset($f[$output])||$f[$output]===true) {
          $this->table[$output][] = $k;
        }
      }
    }
    return $this->table[$output];
  }

  public function select(&$fields = null)
  {
    $select = [];
    if ($fields === null) {
      $fields = $this->fields();
    }

    foreach ($fields as $key => $value) {
      $select[$key] = $this->getColumnKey($value);
      if ($qcolumn = $this->fieldAttr($value, 'jt')) {
        unset($select[$key]);
      }
    }
    return implode(',', $select);
  }

  public function getColumnKey($value, $select=true)
  {
    if ($qcolumn = $this->fieldAttr($value, 'qcolumn')) {
      return $qcolumn.($select? ' as '.$value: '');
    }
    if (@$this->table['fields'][$value]['type'] === 'meta') {
      list($mt, $vt) = $this->getMT($value);
      $this_id = $this->name().".".$this->id();
      $qcolumn = "(SELECT GROUP_CONCAT(`{$mt[3]}`) FROM {$mt[0]} ";
      $qcolumn .= "WHERE {$mt[1]}=$this_id AND {$mt[0]}.{$mt[2]}='{$vt}')";
      return $qcolumn.($select? ' as '.$value: '');
    }
    return '`'.$this->db->res($value).'`';
  }

  public function selectsum($groupby)
  {
    $select = $this->fields();

    foreach ($select as $key => $value) {
      if ($this->fieldAttr($key, 'type') === "number") {
        $select[$key] = 'SUM('.$value.') as '.$value;
      } elseif ($groupby === $value) {
        if ($qcolumn = $this->fieldAttr($value, 'qcolumn')) {
          $select[$key] = $qcolumn.' as '.$value;
        }
        if (@$this->table['fields'][$value]['type'] === 'meta') {
          list($mt, $vt) = $this->getMT($value);
          $this_id = $this->name().".".$this->id();
          $select[$key] = "(SELECT GROUP_CONCAT(`{$mt[3]}`) FROM {$mt[0]} ";
          $select[$key] .= "WHERE {$mt[1]}=$this_id AND {$mt[0]}.{$mt[2]}='{$vt}') as ".$value;
        }
      } else {
        $select[$key] = "'' as ".$value;
      }
    }

    return implode(',', $select);
  }

  public function startIndex($args)
  {
    $ppp = $this->table['pagination'] ?? 25;
    if ($page = $args['page'] ?? 1) {
      return ($page-1)*$ppp;
    }
    return 0;
  }

  public function orderby($orders = null)
  {
    $_orders = [];
    if (is_string($orders)) {
      $orders = explode(',', $orders);
    }

    if ($orders) {
      foreach ($orders as $key=>$order) {
        $order = $this->db->res($order);
        $o = is_numeric($key) ? explode('_', $order) : [$key, $order];
        if (!array_key_exists($o[0], $this->table['fields'])) {
          continue;
        }
        if ($o[1]==='a') {
          $o[1]='ASC';
        }
        if ($o[1]==='d') {
          $o[1]='DESC';
        }
        $_orders[] = $o[0].' '.$o[1];
      }
    }

    $by = $_orders!==[] ? implode(',', $_orders) : $this->id().' DESC';
    return " ORDER BY $by";
  }

  public function groupby($group)
  {
    if ($group===null) {
      if ($group = $this->table['groupby']??null) {
        return " GROUP BY $group";
      }
      return '';
    }
    return " GROUP BY $group";
  }

  public function limit($limit = null)
  {
    if ($limit===null) {
      $limit = $this->startIndex();
      if (isset($this->table['pagination'])) {
        $limit .= ','.$this->table['pagination'];
      } else {
        return '';
      }
    } elseif (is_array($limit)) {
      $limit = implode(',', $limit);
    } elseif ($limit===false) {
      return '';
    }
    return $this->db->res(" LIMIT $limit");
  }

  public function limitPage($args)
  {
    $ppp = $this->table['pagination'] ?? 25;
    if ($page = $args['page'] ?? 1) {
      $offset = ($page-1)*$ppp;
      return " LIMIT $offset, $ppp";
    }
    return "";
  }

  public function event($event, &$data)
  {
    if (isset($this->table['events'])) {
      foreach ($this->table['events'] as $ev) {
        if ($ev[0]===$event) {
          $ev[1]($data);
        }
      }
    }
  }

  public function set(&$fields = null)
  {
    $set = [];
    if ($fields===null) {
      $fields=$_POST;
    }
    if (isset($this->table['filters'])) {
      foreach ($this->table['filters'] as $k=>$f) {
        if (isset($fields[$k])) {
          $fields[$k]=$f;
        }
      }
    }
    $this->event('change', $fields);

    foreach ($fields as $key=>$value) {
      if (array_key_exists($key, $this->table['fields'])) {
        if ($this->fieldAttr($key, 'qcolumn')) {
          continue;
        }
        if ($this->fieldAttr($key, 'type')=='json') {
          $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        if ($allowed = $this->fieldAttr($key, 'allow_tags')) {
          $value = HtmlInput::purify($value, $allowed);
        } else {
          $value = strip_tags($value);
        }
        if (in_array($this->fieldAttr($key, 'type'), ['joins','meta'])) {
          continue;
        }

        if ($value==='') {
          if ($def = $this->fieldAttr($key, 'default')) {
            $value = $def;
          }
        }
        $value = strtr($value, ["'"=>"\'"]);
        $set[] = "$key='$value'";
      }
    }
    if ($set != []) {
      return ' SET '.implode(',', $set);
    }
    return '';
  }

  public function updateJoins($id, &$fields = null)
  {
    if ($fields===null) {
      $fields=$_POST;
    }

    foreach ($fields as $key=>$value) {
      if (@$this->table['fields'][$key]['type'] === 'joins') {
        $jt = $this->table['fields'][$key]["join_table"]??$this->table['fields'][$key]["jt"];
        $arrv = explode(",", $value);
        $this->db->query("DELETE FROM {$jt[0]} WHERE `{$jt[1]}`=?;", [$id]);
        foreach ($arrv as $arrv_k=>$arrv_v) {
          $this->db->query("INSERT INTO {$jt[0]}(`{$jt[1]}`,`{$jt[2]}`)
          VALUES(?,?);", [$id,$arrv_v]);
        }
        continue;
      }
    }
  }

  public function updateMeta($id, &$fields = null)
  {
    if ($fields===null) {
      $fields=$_POST;
    }

    foreach ($fields as $key=>$value) {
      if (@$this->table['fields'][$key]['type'] === 'meta') {
        list($mt, $vt) = $this->getMT($key);
        if (is_string($value)) {
          if (@$this->table['fields'][$key]['values'] === 1) {
            $arrv = [$value];
          } elseif ($value!==null) {
            $arrv = explode(",", $value);
          }
        } else {
          $arrv = $value;
        }
        $this->db->query("DELETE FROM {$mt[0]} WHERE `{$mt[1]}`=? AND `{$mt[2]}`=?;", [$id,$vt]);
        if ($arrv) {
          foreach ($arrv as $arrv_k=>$arrv_v) {
            if ($arrv_v!='' && $arrv_v!=null) {
              if ($allowed = $this->fieldAttr($key, 'allow_tags')) {
                $arrv_v = HtmlInput::purify($arrv_v, $allowed);
              } else {
                $arrv_v = strip_tags($arrv_v);
              }
              $this->db->query("INSERT INTO {$mt[0]}(`{$mt[1]}`,`{$mt[3]}`,`{$mt[2]}`)
              VALUES(?,?,?);", [$id,$arrv_v,$vt]);
            }
          }
        }
        continue;
      }
    }
  }

  public function getMT($key)
  {
    $vt = $this->table['fields'][$key]["meta_key"]??$this->table['fields'][$key]["metatype"];
    if (isset($this->table['fields'][$key]['meta_table'])) {
      $mt = $this->table['fields'][$key]['meta_table'];
    } elseif (isset($this->table['meta_table'])) {
      $mt = $this->table['meta_table'];
    }
    $vt = is_array($vt)? $vt[1]: $vt;
    return [$mt, $vt];
  }

  public function where($fields = null)
  {
    $filters = [];
    if ($fields===null) {
      return '';
    }
    if (isset($this->table['filters'])) {
      foreach ($this->table['filters'] as $k=>$f) {
        $fields[$k]=$f;
      }
    }

    foreach ($fields as $key=>$value) {
      if (!is_numeric($key)) {
        if (array_key_exists($key, $this->table['fields'])) {
          if (is_array($value)) {
            $key = $this->getColumnKey($key, false);
            foreach ($value as $subkey=>$subvalue) {
              $subvalue = $this->db->res($subvalue);
              if (isset(self::$basicOps[$subkey])) {
                $filters[] = $key . self::$basicOps[$subkey] . $subvalue;
                continue;
              }
              if ($subkey === 'gts') {
                $filters[] = "$key>'$subvalue'";
              }
              if ($subkey === 'lts') {
                $filters[] = "$key<'$subvalue'";
              }
              if ($subkey === 'begin') {
                $filters[] = "$key like '$subvalue%'";
              }
              if ($subkey === 'end') {
                $filters[] = "$key like '%$subvalue'";
              }
              if ($subkey === 'has') {
                $filters[] = "$key like '%$subvalue%'";
              }
              if ($subkey === 'in') {
                $filters[] = "$key IN($subvalue)";
              }
              if ($subkey === 'inset') {
                $filters[] = "FIND_IN_SET($subvalue, $key)>0";
              }
            }
          } elseif (@$this->table['fields'][$key]['type']=='meta') {
            $key = $this->getColumnKey($key, false);
            if ($value==null) {
              $filters[] = "$key IS NULL";
            } else {
              $value = $this->db->res($value);
              $filters[] = "FIND_IN_SET($value, $key)>0";
            }
          } else {
            $ckey = $this->getColumnKey($key, false);
            if (@$this->table['fields'][$key]['type']=='date') {
              $ckey = "SUBSTRING($key,1,10)";
            }
            if ($value==null) {
              $filters[] = "$ckey IS NULL";
            } else {
              $value = $this->db->res($value);
              $filters[] = "$ckey='$value'";
            }
          }
        }
      }
    }

    if (isset($fields["search"])) {
      $value = $this->db->res($fields["search"]);
      $search_filter = [];
      foreach ($this->table['fields'] as $key=>$field) {
        if (!isset($field['qcolumn']) && !isset($field['meta_key']) && !isset($field['metatype'])) {
          $search_filter[] = "$key LIKE '%{$value}%'";
        }
      }
      $filters[] = '('.implode(' OR ', $search_filter).')';
    }

    if ($filters != []) {
      return ' WHERE '.implode(' AND ', $filters);
    }
    return '';
  }

  public function can($action, $field = null)
  {
    $array = $this->table['permissions'][$action];

    if ($field!=null && isset($this->table['fields'][$field]['permissions'])) {
      $array = $this->table['fields'][$field]['permissions'][$action];
    }

    if (is_bool($array)) {
      return $array;
    }
    if (!is_array($array)) {
      if (is_callable($array)) {
        return $array();
      }
      $array = explode(' ', $array);
    }

    foreach ($array as $value) {
      if (in_array($value, $this->permissions)) {
        return true;
      }
    }

    return false;
  }

  public function update()
  {
    TableSchema::update($this->table);
    return true;
  }

  public function getTable()
  {
    return $this->table;
  }

  public function getFields($output = '')
  {
    if ($output==='') {
      return $this->table->fields;
    }
    $fields = [];
    foreach ($this->fields($output) as $fkey) {
      $fields[$fkey] = $this->table['fields'][$fkey];
    }
    return $fields;
  }

  public function getEmpty()
  {
    $row = [];
    foreach ($this->fields('create') as $key) {
      $fv="";
      $field = $this->table['fields'][$key];
      if (isset($field['default'])) {
        $fv=$field['default'];
      } elseif (isset($field['type'])) {
        if (isset($field['options'])) {
          $fv="0";
        }
        if ($field['type']==="date") {
          $fv=date("Y-m-d");
        }
        if ($field['type']==="number") {
          $fv="0";
        }
      }
      $row[]=$fv;
      $row[$key]=$fv;
    }
    return $row;
  }

  public function getMeta($id, $type = null)
  {
    global $db;
    if (!isset($this->table['meta_table'])) {
      return null;
    }
    $m = $this->table['meta_table'];
    if ($type!==null) {
      return $db->getList("SELECT {$m[3]} FROM {$m[0]}
        WHERE {$m[1]}=? AND $m[2]=?;", [$id, $type]);
    } else {
      $list = [];
      $gen = $db->read()->gen("SELECT {$m[2]},{$m[3]} FROM {$m[0]} WHERE {$m[1]}=?;", $id);
      foreach ($gen as $row) {
        @$list[$row[0]][] = $row[1];
      }
      return $list;
    }
  }

  public function getRow($filters, $args = [])
  {
    $args['limit'] = 1;
    return $this->getRows($filters, $args)[0] ?? null;
  }

  public function getRows($filters = [], $args = [])
  {
    global $db;
    if (!$this->can('read')) {
      return [];
    }

    $where = $this->where($filters);
    $select = isset($args['select']) ? $this->select($args['select']) : $this->select();
    // TODO selected meta should translate in subqueries
    $groupby = $this->groupby($args['groupby']??null);
    $orderby = isset($args['orderby']) ? $this->orderby($args['orderby']) : $this->orderby();
    $limit = isset($args['limit']) ? $this->limit($args['limit']) : $this->limitPage($args);
    $res = $db->read()->getAssoc("SELECT $select
      FROM {$this->name()}$where$groupby$orderby$limit;");
    return $res;
  }

  public function getRowsIndexed($filters = [], $args = [])
  {
    $rows = $this->getRows($filters, $args);
    foreach ($rows as &$row) {
      $row = array_values($row);
    }
    return $rows;
  }

  public function getAllRows($args = [])
  {
    return $this->getRows(null, $args);
  }

  public function totalRows(&$filters = [])
  {
    global $db;
    if (!$this->can('read')) {
      return;
    }
    $where = $this->where($filters);
    $res = $db->read()->value("SELECT COUNT(*) FROM {$this->name()}$where;");
    return (int)$res;
  }

  public function deleteRow($id)
  {
    $this->event('delete', $id);
    $res = $this->db->query("DELETE FROM {$this->name()} WHERE {$this->id()}=?;", $id);
  }

  public function createRow($data = [])
  {
    if ($this->can('create') === false) {
      return false;
    }
    $insert_fields = [];
    $insert_values = [];
    $binded_values = [];
    $this->event('create', $data);
    if ($data===false) {
      return 0;
    }

    foreach ($this->table['fields'] as $field=>$value) {
      if (isset($value['qtype']) && isset($data[$field])) {
        $insert_fields[] = '`'.$field.'`';
        $insert_values[] = $data[$field];
        $binded_values[] = '?';
      }
    }
    $fnames = implode(',', $insert_fields);
    $binded = implode(',', $binded_values);
    $q = "INSERT INTO {$this->name()}($fnames) VALUES($binded);";
    if (!empty($insert_values)) {
      $res = $this->db->query($q, $insert_values);
    } else {
      $res = $this->db->query($q);
    }
    $data['id'] = $this->db->insert_id;
    $this->event('created', $data);
    return $data['id'];
  }
}

class_alias('Gila\\Table', 'gTable');
