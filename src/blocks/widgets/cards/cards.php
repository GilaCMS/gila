<div style="text-align:center">
  <h2><?=$data['heading']?></h2>
  <p><?=$data['text']?></p>
</div>
<div class="gm-grid" style="justify-items:center;margin-bottom:1em">
<?php
  foreach (json_decode($data['cards'], true) as $key=>$card) {
    echo '<div class="g-card wrapper bg-white" style="text-align:center;max-width:300px">';
    if ($card[0]) {
      echo '<div class="g-card-image" style="padding:0 25%">';
      echo View::img($card[0], 'c_', 400);
      echo '</div>';
    }
    echo '<h4 style="margin:2px"><b>'.$card[1].'</b></h4><p>'.$card[2].'</p>';
    if ($card[3] && $card[4]) {
      echo '<a class="g-btn" href="'.$card[4].'">'.$card[3].'</a>';
    }
    echo '</div>';
  }
?>
</div>

