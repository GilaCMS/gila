<?=View::css('core/gila.min.css')?>
<section>
<div style="text-align:center">
  <h2><?=$data['heading']?></h2>
  <p><?=$data['text']?></p>
</div>
<div class="gm-grid" style="display:grid; grid-gap:1em; grid-template-columns:repeat(auto-fit, minmax(300px,1fr));
justify-items:center;margin-bottom:1em;overflow: auto;padding: 2em 0;">
<?php
  foreach (json_decode($data['cards'], true) as $key=>$card) {
    echo '<div class="g-card wrapper bg-white" style="box-shadow: 0 2px 5px 0 rgba(0,0,0,0.15);max-width:300px;text-align:'.htmlentities($data['align']??'center').'">';
    if ($card[0]) {
      echo '<div class="g-card-image" style="display:inline-block;max-width:50%">';
      echo View::img($card[0], 'c_', 200);
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
</section>
