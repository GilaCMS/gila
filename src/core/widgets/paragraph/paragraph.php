<?php

if(substr($widget_data->text,0,3)==='<p>') {
  echo $widget_data->text;
} else {
  echo '<p>'.$widget_data->text.'</p>';
}
