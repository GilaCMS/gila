<?php

class posts extends controller
{

  function indexAction ()
  {
      echo "<table class=\"pnk-table\"><tr><th>ID<th>Title<th>Slug<th>Post<th>User ID<th>Updated";
      $res = $this->db->query("SELECT * FROM post");
      while ($r = mysqli_fetch_array($res)) {
          echo '<tr>'.'<td>'.$r['id'].'<td>'.$r['title'].'<td>'.$r['slug'].'<td>'.$r['post'].'<td>'.$r['user_id'].'<td>'.$r['updated'];
      }
      echo "</table>";
  }

}
