<?php

class gRow
{
  private $table;
  private $filters;
  private $select;
  private $row;

  function __construct (gTable $table, $filters, $select=null, $limit=null)
  {
    $this->table = $table;
    $this->filters = $filters;
    $this->select = $select;
    return $this;
  }

  function get ($key)
  {
    global $db;
    if(isset($row)) {
      return $row[$key] ?? null;
    }
    $res = $db->getAssoc("SELECT {$pnk->select()} FROM {$pnk->name()}{$pnk->where($_GET)}{$pnk->orderby()}{$pnk->limit()};");
  }

  function set ($key, $value)
  {
  }

}
