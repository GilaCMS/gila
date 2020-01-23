<?php

class gTable
{
  private $table;
  private $permissions;
  static public $tableList = [];

  function __construct ($content, $permissions = ['admin'])
  {
    if(isset(self::$tableList[$content])) {
      return self::$tableList[$content];
    } 
    if(isset(gila::$content[$content]))
      $path = 'src/'.gila::$content[$content];
    else if(file_exists($content))
      $path = $content;
    else
      $path = 'src'.$content;
 
    $this->table = include $path;
    if(isset($table)) $this->table = $table;

    $this->permissions = $permissions;
    if($patch = @gila::$contentField[$this->table['name']]) { //depraciated from 1.8.0
      $this->table['fields'] = array_merge($this->table['fields'],$patch);
    }
    if(isset(gila::$contentInit[$this->table['name']])) {
      foreach(@gila::$contentInit[$this->table['name']] as $init) {
        $init($this->table);
      }
    }
    if(isset($this->table['lang'])) gila::addLang($this->table['lang']);
    foreach ($this->table['fields'] as $key => &$field) {
      if(isset($field['qoptions'])) {
        global $db;
        if(!isset($field['options'])) $field['options'] = [];
        $res = $db->get($field['qoptions']);
        foreach($res as $r) $field['options'][$r[0]] = $r[1];
      }
      if(isset($field['title'])) {
        $field['title'] = __($field['title']);
      } else $field['title'] = __($key);
    }
    if(isset($this->table['children'])) foreach ($this->table['children'] as $key => &$child) {
      $child_table = new gTable($key,$permissions);
      $child['table'] = $child_table->getTable();
    }
    $this->table['title'] = __($this->table['title']);

    if(!isset($this->table['permissions'])) $this->table['permissions'] = [];
    $p = &$this->table['permissions'];
    if(!isset($p['create'])) $p['create'] = ['admin'];
    if(!isset($p['read'])) $p['read'] = ['admin'];
    if(!isset($p['update'])) $p['update'] = ['admin'];
    if(!isset($p['delete'])) $p['delete'] = ['admin'];

    self::$tableList[$content] = &$this;
  }

  function name()
  {
    return $this->table['name'];
  }

  function id()
  {
    return $this->table['id'] ?? ‘id’; 
  }

  function fieldAttr($field, $attr)
  {
    if(isset($this->table['fields'][$field]))
      if(isset($this->table['fields'][$field][$attr]))
        return $this->table['fields'][$field][$attr];
    return false;
  }

  function fields($output = 'list')
  {
    if(!isset($this->table[$output])) {
      $this->table[$output]=[];
      foreach($this->table['fields'] as $k=>$f) {
        if(!isset($f[$output])||$f[$output]===true) $this->table[$output][] = $k;
      }
    }
    return $this->table[$output];
  }

  function select(&$fields = null)
  {
    global $db;
    $select = [];
    if($fields == null) $fields = $this->fields();

    foreach ($fields as $key => $value) {
      $select[$key] = '`'.$db->res($value).'`';
      if($qcolumn = $this->fieldAttr($value, 'qcolumn')) {
        $select[$key] = $qcolumn.' as '.$value;
      }
      if($mt = $this->fieldAttr($value, 'mt')) {
        $vt = $this->fieldAttr($value, 'metatype');
        $this_id = $this->name().".".$this->id();
        $select[$key] = "(SELECT GROUP_CONCAT(`{$mt[2]}`) FROM {$mt[0]} ";
        $select[$key] .= "WHERE {$mt[1]}=$this_id AND {$mt[0]}.{$vt[0]}='{$vt[1]}') as ".$value;
      }
      if($qcolumn = $this->fieldAttr($value, 'jt')) {
        unset($select[$key]);
      }
    }
    return implode(',', $select);
  }

  function selectsum($groupby)
  {
    $select = $this->fields();

    foreach ($select as $key => $value) {
      if($this->fieldAttr($key, 'type') == "number") {
        $select[$key] = 'SUM('.$value.') as '.$value;
      } else if($groupby == $value) {
        if($qcolumn = $this->fieldAttr($value, 'qcolumn')) {
          $select[$key] = $qcolumn.' as '.$value;
        }
        if($mt = $this->fieldAttr($value, 'mt')) {
          $vt = $this->fieldAttr($value, 'metatype');
          $this_id = $this->name().".".$this->id();
          $select[$key] = "(SELECT GROUP_CONCAT(`{$mt[2]}`) FROM {$mt[0]} ";
          $select[$key] .= "WHERE {$mt[1]}=$this_id AND {$mt[0]}.{$vt[0]}='{$vt[1]}') as ".$value;
        }
      } else {
        $select[$key] = "'' as ".$value;
      }
    }

    return implode(',', $select);
  }

  function startIndex($args) {
    $ppp = $this->table['pagination'] ?? 25;
    if($page = $args['page'] ?? 1) {
      return ($page-1)*$ppp;
    }
    return 0;
  }

  function orderby($orders = null) {
    global $db;
    $_orders = [];
    if(is_string($orders)) {
      $orders = explode(',', $orders);
    }

    if($orders) foreach($orders as $key=>$order) {
      $order = $db->res($order);
      $o = is_numeric($key) ? explode('_', $order) : [$key, $order];
      if(!array_key_exists($o[0], $this->table['fields'])) continue;
      if($o[1]=='a') $o[1]='ASC';
      if($o[1]=='d') $o[1]='DESC';
      $_orders[] = $o[0].' '.$o[1];
    }

    $by = $_orders!==[] ? implode(',', $_orders) : $this->id().' DESC';
    return " ORDER BY $by";
  }

  function groupby($group) {
    return " GROUP BY $group";
  }

  function limit($limit = null) {
    global $db;
    if($limit==null) {
      $limit = $this->startIndex();
      if(isset($this->table['pagination'])) {
        $limit .= ','.$this->table['pagination'];
      } else return "";
    } else if(is_array($limit)) {
      $limit = implode(',', $limit);
    }
    return $db->res(" LIMIT $limit");
  }

  function limitPage($args) {
    $ppp = $this->table['pagination'] ?? 25;
    if($page = $args['page'] ?? 1) {
      $offset = ($page-1)*$ppp;
      return " LIMIT $offset, $ppp";
    }
    return "";
  }

  function event($event, &$data) {
    if(isset($this->table['events'])) foreach($this->table['events'] as $ev) {
      if($ev[0]==$event) {
        $ev[1]($data);
      }
    }
  }

  function set(&$fields = null) {
    $set = [];
    if($fields==null) $fields=$_POST;
    foreach(@$this->table['filters'] as $k=>$f) $fields[$k]=$f;
    $this->event('change', $fields);

    foreach($fields as $key=>$value) {
      if(array_key_exists($key, $this->table['fields'])) {
        if ($this->fieldAttr($key, 'qcolumn')) continue;
        if ($allowed = $this->fieldAttr($key, 'allow-tags')) {
          if($allowed!=true)
            $value=strip_tags($value,$allowed);
        } else {
          $value=strip_tags($value);
        }
        if (in_array($this->fieldAttr($key, 'type'),['joins','meta'])) continue;

        if(is_array($value)) {
          foreach($value as $subkey=>$subvalue) {
            if($subkey == 'fn') $set[] = "$key=$subvalue";
            if($subkey == 'add') $set[] = "$key=$key+$subvalue";
          }
        } else {
          if($value=='') if($def = $this->fieldAttr($key, 'default')) $value = $def;
          $value = str_replace("'","\'",$value);
          $set[] = "$key='$value'";
        }
      }
    }
    if($set != []) {
      return ' SET '.implode(',',$set);
    }
    return '';
  }

  function updateJoins ($id, &$fields = null) {
    global $db;
    if($fields==null) $fields=$_POST;

    foreach($fields as $key=>$value) {
      if(@$this->table['fields'][$key]['type'] == 'joins') {
        $jt = $this->table['fields'][$key]["jt"];
        $arrv = explode(",",$value);
        $db->query("DELETE FROM {$jt[0]} WHERE `{$jt[1]}`='$id' AND `{$jt[2]}` NOT IN($value);");
        foreach($arrv as $arrv_k=>$arrv_v){
          $db->query("INSERT INTO {$jt[0]}(`{$jt[1]}`,`{$jt[2]}`) VALUES('$id','$arrv_v');");
        }
        continue;
      }
    }
  }

  function updateMeta ($id, &$fields = null) {
    global $db;
    if($fields==null) $fields=$_POST;

    foreach($fields as $key=>$value) {
      if(@$this->table['fields'][$key]['type'] == 'meta') {
        $mt = $this->table['fields'][$key]["mt"];
        $vt = $this->table['fields'][$key]["metatype"];
        if(is_string($value)) {
          if(@$this->table['fields'][$key]['values'] == 1) {
            $arrv = [$value];
          } else {
            $arrv = explode(",",$value);
          }
        } else $arrv = $value;
        $db->query("DELETE FROM {$mt[0]} WHERE `{$mt[1]}`='$id' AND `{$vt[0]}`='{$vt[1]}';");
        foreach($arrv as $arrv_k=>$arrv_v) if($arrv_v!='' && $arrv_v!=null) {
          $arrv_v = strip_tags($arrv_v);
          $db->query("INSERT INTO {$mt[0]}(`{$mt[1]}`,`{$mt[2]}`,`{$vt[0]}`) VALUES('$id','$arrv_v','{$vt[1]}');");
        }
        continue;
      }
    }

  }

  function where($fields = null) {
    global $db;
    $filters = [];
    if($fields==null) return '';
    if(isset($this->table['filters'])) {
      foreach($this->table['filters'] as $k=>$f) $fields[$k]=$f;
    }

    foreach($fields as $key=>$value) if(!is_numeric($key)) {
      if(array_key_exists($key, $this->table['fields'])) {
        if(is_array($value)) {
          foreach($value as $subkey=>$subvalue) {
            $subvalue = $db->res($subvalue);
            if($subkey == 'gt') $filters[] = "$key>$subvalue";
            if($subkey == 'ge') $filters[] = "$key>=$subvalue'";
            if($subkey == 'lt') $filters[] = "$key<$subvalue";
            if($subkey == 'le') $filters[] = "$key<=$subvalue";
            if($subkey == 'gts') $filters[] = "$key>'$subvalue'";
            if($subkey == 'lts') $filters[] = "$key<'$subvalue'";
            if($subkey == 'begin') $filters[] = "$key like '$subvalue%'";
            if($subkey == 'end') $filters[] = "$key like '%$subvalue'";
            if($subkey == 'has') $filters[] = "$key like '%$subvalue%'";
          }
        } else {
          $value = $db->res($value);
          $filters[] = "$key='$value'";
        }
      }
    }

    if(isset($fields["search"])) {
      $value = $db->res($fields["search"]);
      $search_filter = [];
      foreach($this->table['fields'] as $key=>$field) {
        if(!isset($field['qcolumn']) && !isset($field['metatype']))
          $search_filter[] = "$key LIKE '%{$value}%'";
      }
      $filters[] = '('.implode(' OR ',$search_filter).')';
    }

    if($filters != []) {
      return ' WHERE '.implode(' AND ',$filters);
    }
    return '';
  }

  function can($action, $field = null) {
    $array = $this->table['permissions'][$action];

    if($field!=null && isset($this->table['fields'][$field]['permissions'])) {
      $array = $this->table['fields'][$field]['permissions'][$action];
    }

    if(is_bool($array)) return $array;
    if(!is_array($array)) {
      if(is_callable($array)) return $array();
      $array = explode(' ', $array);
    }

    foreach($array as $value) {
      if(in_array($value, $this->permissions)) return true;
    }
    return false;
  }

  function update() {
    global $db;
    $tname = $this->table['name'];
    $table_created=false;
    $id = $this->id();

    // CREATE TABLE
    $qtype = @$this->table['fields'][$id]['qtype']?:'INT NOT NULL AUTO_INCREMENT';
    $ql = "CREATE TABLE IF NOT EXISTS $tname($id $qtype,PRIMARY KEY (`$id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    $db->query($ql);

    // ADD COLUMNS
    foreach($this->table['fields'] as $fkey=>$field) {
      if(isset($field['qtype']) && $fkey!=$id) {
        $column = @$field['qcolumn']?:$fkey;
        if (strpos($column, '(') === false) {
          $db->query("ALTER TABLE $tname ADD $column {$field['qtype']};");
        }
      }
    }

    // ADD KEYS
    if(isset($this->table['qkeys'])) foreach($this->table['qkeys'] as $key)
      $db->query("ALTER TABLE $tname ADD KEY `$key` (`$key`);");

    return true;
  }

  function getTable()
  {
    return $this->table;
  }

  function getFields($output = '') {
    if($output=='') return $this->table->fields;
    $fields = [];
    foreach($this->fields($output) as $fkey) {
      $fields[$fkey] = $this->table['fields'][$fkey];
    }
    return $fields;
  }

  function getEmpty() {
    $row = [];
    foreach($this->fields('create') as $key) {
      $fv="";
      $field = $this->table['fields'][$key];
      if(isset($field['default'])) {
        $fv=$field['default'];
      } else if(isset($field['type'])) {
        if(isset($field['options'])) $fv="0";
        if($field['type']=="date") $fv=date("Y-m-d");
        if($field['type']=="number") $fv="0";
      }
      $row[]=$fv;
      $row[$key]=$fv;
    }
    return $row;
  }

  function getRow($filters, $args = [])
  {
    $args['limit'] = 1;
    return $this->getRows($filters, $args)[0] ?? null;
  }

  function getRows($filters = [], $args = [])
  {
    global $db;
    if(!$this->can('read')) return;
    $where = $this->where($filters);
    $select = isset($args['select']) ? $this->select($args['select']) : $this->select();
    $orderby = isset($args['orderby']) ? $this->orderby($args['orderby']) : $this->orderby();
    $limit = isset($args['limit']) ? $this->limit($args['limit']) : $this->limitPage($args);
    $res = $db->getAssoc("SELECT $select
      FROM {$this->name()}$where$orderby$limit;");
    return $res;
  }

  function getRowsIndexed($filters = [], $args = []) {
    $rows = $this->getRows($filters, $args);
    foreach($rows as &$row) {
      $row = array_values($row);
    }
    return $rows;
  }

  function getAllRows($args = []) {
    return $this->getRows(null, $args);
  }

  function totalRows(&$filters = [])
  {
    global $db;
    if(!$this->can('read')) return;
    $where = $this->where($filters);
    $res = $db->value("SELECT COUNT(*) FROM {$this->name()}$where;");
    return $res;
  }

  function deleteRow($id)
  {
    global $db;
    $this->event('delete', $id);
    $res = $db->query("DELETE FROM {$this->name()} WHERE {$this->id()}=?;", $id);
  }

}
