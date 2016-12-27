<?php

class settings extends controller
{

  function indexAction ($args)
  {
      echo "<div><h3>Website</h3>";
      foreach ($GLOBALS['config'] as $key => $value) {
          echo '<div class="col-1"></div><label class="col-2">'.ucwords($key).'</label><input value="'.$value.'" class="col-3" />';
      }
      echo "</div>";
      echo "<div><h3>Themes</h3>";
      foreach ($GLOBALS['path']['theme'] as $key => $value) {
          echo '<div class="col-1"></div><label class="col-2">'.ucwords($key).'</label><input value="'.$value.'" class="col-3" />';
      }
      echo "</div>";
      echo "<div><h3>Default values</h3>";
      foreach ($GLOBALS['default'] as $key => $value) {
          echo '<div class="col-1"></div><label class="col-2">'.ucwords($key).'</label><input value="'.$value.'" class="col-3 /">';
      }
      echo "</div>";
  }


}
