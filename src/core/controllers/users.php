<?php

class users extends controller
{

  function indexAction ($args)
  {
      echo "<table class=\"pnk-table\"><tr><th>ID<th>Name<th>Email<th>Pass";
      $res = $this->db->query("SELECT * FROM user");
      while ($r = mysqli_fetch_array($res)) {
          echo '<tr>'.'<td>'.$r['id'].'<td>'.$r['name'].'<td>'.$r['email'].'<td>'.$r['pass'];
      }
      echo "</table>";
  }


}
