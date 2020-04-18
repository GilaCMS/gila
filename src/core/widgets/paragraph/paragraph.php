<?php

if(substr($data['text'],0,3)==='<p>') {
  echo $data['text'];
} else {
  echo '<p>'.$data['text'].'</p>';
}
