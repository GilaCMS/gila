<?php

class pnkTable
{
    private $table;
    private $permissions;

    function __construct ($path, $permissions = ['admin'])
    {
        include $path;
        $this->table = $table;
        $this->permissions = $permissions;

        if(!isset($this->table['permissions'])) $this->table['permissions'] = [];
        $p = &$this->table['permissions'];
        if(!isset($p['create'])) $p['create'] = ['admin'];
        if(!isset($p['read'])) $p['read'] = ['admin'];
        if(!isset($p['update'])) $p['update'] = ['admin'];
        if(!isset($p['delete'])) $p['delete'] = ['admin'];
    }

    function name()
    {
        return $this->table['name'];
    }

    function id()
    {
        return $this->table['id'];
    }

    function fieldAttr($field, $attr)
    {
        if(isset($this->table['fields'][$field]))
            if(isset($this->table['fields'][$field][$attr]))
                return $this->table['fields'][$field][$attr];
        return false;
    }

    function fields()
    {
        if(!isset($this->table['list'])) {
            $this->table['list']=[];
            foreach($this->table['fields'] as $k=>$f) {
                if(!isset($f['list'])||$f['list']==true) $this->table['list'][] = $k;
            }
        }
        return $this->table['list'];
        //if(isset($_GET['$select'])) {
        //    $select = explode(',', $_GET['$select']);
        //}
    }

    function select()
    {
        $select = $this->fields();

        foreach ($select as $key => $value) {
            if($qcolumn = $this->fieldAttr($value, 'qcolumn'))
                $select[$key] = $qcolumn.' as '.$value;
            if($mt = $this->fieldAttr($value, 'mt')) {
                $vt = $this->fieldAttr($value, 'metatype');
                $this_id = $this->name().".".$this->table['id'];
                $select[$key] = "(SELECT GROUP_CONCAT(`{$mt[2]}`) FROM {$mt[0]} WHERE {$mt[1]}=$this_id AND {$mt[0]}.{$vt[0]}='{$vt[1]}') as ".$value;
            }
            if($qcolumn = $this->fieldAttr($value, 'jt'))
                unset($select[$key]);
        }
        return implode(',', $select);
    }

    function selectsum($groupby)
    {
        $select = $this->fields();

        foreach ($select as $key => $value) {
            if($this->table['fields'][$value]['type']=="number") {
                $select[$key] = 'SUM('.$value.') as '.$value;
            } else if($groupby == $value) {
                if($qcolumn = $this->fieldAttr($value, 'qcolumn'))
                    $select[$key] = $qcolumn.' as '.$value;
                if($mt = $this->fieldAttr($value, 'mt')) {
                    $vt = $this->fieldAttr($value, 'metatype');
                    $this_id = $this->name().".".$this->table['id'];
                    $select[$key] = "(SELECT GROUP_CONCAT(`{$mt[2]}`) FROM {$mt[0]} WHERE {$mt[1]}=$this_id AND {$mt[0]}.{$vt[0]}='{$vt[1]}') as ".$value;
                }
            } else $select[$key] = "'' as ".$value;
        }
        return implode(',', $select);
    }

    function startIndex() {
        $ppp = @$this->table['pagination']?:25;
        return isset($_GET['page'])? ($_GET['page']-1)*$ppp :0;
    }

    function orderby($id=null, $v='desc') {
        if(isset($_GET['orderby'])) {
            $o = explode('_',$_GET['orderby']);
            if(array_key_exists($o[0], $this->table['fields'])) {
                $id=$o[0];
                if(isset($o[1])) {
                    if($o[1]=='a') $v='asc';
                    if($o[1]=='d') $v='desc';
                }
            }
        }
        if($id==null) $id = @$this->table['id']?:'id';
        return " ORDER BY $id $v";
    }

    function groupby($group) {
        return " GROUP BY $group";
    }

    function limit($start=null,$end=null) {
        if($start==null) $start = $this->startIndex();
        if($end==null) if(isset($this->table['pagination'])) $end=$this->table['pagination'];
        if($end==null) return "";
        return " LIMIT $start,$end";
    }


    function set(&$fields = null) {
        $set = [];
        if($fields==null) $fields=$_POST;

        foreach($fields as $key=>$value) {
            if(array_key_exists($key, $this->table['fields'])) {
                if(is_array($value)) {
                    foreach($value as $subkey=>$subvalue) {
                        if($subkey == 'fn') $set[] = "$key=$subvalue";
                        if($subkey == 'add') $set[] = "$key=$key+$subvalue";
                    }
                } else {
                    $set[] = "$key='$value'";
                }
            }
        }
        if($set != []) {
            return ' SET '.implode(',',$set);
        }
        return '';
    }


    function where(&$fields = null) {
        $filters = [];
        if($fields==null) $fields=$_GET;

        foreach($fields as $key=>$value) if(!is_numeric($key)){
            if(array_key_exists($key, $this->table['fields'])) {
                if(is_array($value)) {
                    foreach($value as $subkey=>$subvalue) {
                        if($subkey == 'gt') $filters[] = "$key>$subvalue";
                        if($subkey == 'ge') $filters[] = "$key>=$subvalue'";
                        if($subkey == 'lt') $filters[] = "$key<$subvalue";
                        if($subkey == 'le') $filters[] = "$key<=$subvalue";
                        if($subkey == 'begin') $filters[] = "$key like '$subvalue%'";
                        if($subkey == 'end') $filters[] = "$key like '%$subvalue'";
                        if($subkey == 'has') $filters[] = "$key like '%$subvalue%'";
                    }

                } else {
                    $filters[] = "$key='$value'";
                }
            }
        }

        if($filters != []) {
            return ' WHERE '.implode(' AND ',$filters);
        }
        return '';
    }

    function getEmpty() {
        $row = [];
        foreach($this->fields() as $key) {
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
        }
        return $row;
    }

    function can($action, $field = null) {
        $array = $this->table['permissions'][$action];

        if($field!=null && isset($this->table['fields'][$field]['permissions'])) {
            $array = $this->table['fields'][$field]['permissions'][$action];
        }

        if(is_bool($array)) return $array;
        if(is_callable($array)) return $array();

        foreach($array as $value) {
            if(in_array($value, $this->permissions)) return true;
        }
        return false;
    }
}
