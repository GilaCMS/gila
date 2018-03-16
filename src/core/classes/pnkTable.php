<?php

class pnkTable
{
    private $table;
    private $permissions;

    function __construct ($path, $permissions = [])
    {
        include $path;
        $this->table = $table;
        $this->permissions = $permissions;
        if(!isset($this->table['permissions'])) {
            $this->table['permissions'] = [
                'create'=>false,
                'read'=>true,
                'update'=>false,
                'delete'=>false
            ];
        }
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

    function select()
    {
        $select = array_keys($this->table['fields']);
        if(isset($_GET['$select'])) {
            $select = explode(',', $_GET['$select']);
        }
        foreach ($select as $key => $value) {
            if($qcolumn = $this->fieldAttr($value, 'qcolumn'))
                $select[$key] = $qcolumn.' as '.$value;
            if($mt = $this->fieldAttr($value, 'mt')) {
                $vt = $this->fieldAttr($value, 'metatype');
                $this_id = $this->name().".".$this->table['id'];
                //$select[$key] = "(SELECT GROUP_CONCAT({$mt[2]}) FROM {$mt[0]} WHERE {$mt[1]}=$this_id AND {$mt[0]}.{$vt[0]}='{$vt[1]}') as ".$value;
                $select[$key] = "(SELECT GROUP_CONCAT({$mt[2]}) FROM {$mt[0]} WHERE {$mt[1]}=$this_id AND {$mt[0]}.{$vt[0]}='{$vt[1]}') as ".$value;
            }
            if($qcolumn = $this->fieldAttr($value, 'jt'))
                unset($select[$key]);
        }
        return implode(',', $select);
    }

    function orderby() {
        if(isset($_GET['$order'])) {
            $o = explode(' ',$_GET['$order']);
            if(array_key_exists($o[0], $this->table['fields']))
                if(in_array($o[1],['desc','asc']))
                    return ' ORDER BY '.$_GET['$order'];
        }
        return '';
    }


    function set() {
        $set = [];
        foreach($_POST as $key=>$value) {
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
            echo $key."<br>";
        }
        if($set != []) {
            return ' SET '.implode(',',$set);
        }
        return '';
    }


    function where() {
        $filters = [];

        foreach($_GET as $key=>$value) if(!is_numeric($key)){
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

    function can($action, $field = null) {
        $array = $this->table['permissions'][$action];
        if($field!=null && isset($this->table['fields'][$field]['permissions'])) {
            $array = $this->table['fields'][$field]['permissions'][$action];
        }
        if(is_bool($array)) return;

        foreach($array as $value) {
            if(in_array($value, $this->permissions)) return true;
        }
        return false;
    }
}
