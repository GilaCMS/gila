<?php

class users extends controller
{

  function indexAction ()
  {
      global $db;
      echo "<table class=\"pnk-table\"><tr><th>ID<th>Name<th>Email<th>Pass<th>";
      /*$res = $this->db->query("SELECT * FROM user");
      while ($r = mysqli_fetch_array($res)) {
          echo '<tr>'.'<td>'.$r['id'].'<td>'.$r['name'].'<td>'.$r['email'].'<td>'.$r['pass'].'<td><a href="admin/users/edit/'.$r['id'].'">Edit</a>';
      }
      */
      $gen = $db->gen("SELECT * FROM user");
      foreach ($gen as $r) {
          echo '<tr>'.'<td>'.$r['id'].'<td>'.$r['name'].'<td>'.$r['email'].'<td>'.$r['pass'].'<td><a href="admin/users/edit/'.$r['id'].'">Edit</a>';
      }
      echo "</table>";
  }


  function editAction () {
      echo "Edit post ".router::get('id',0);
  }
}
