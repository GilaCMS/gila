<ul class="g-nav pagination">
<?php
  if(!isset($page)) $page=1;
  $totalpages = blog::totalpages();
  if($page>5) echo '<li><a href="'.router::url().'?page=1">1</a></li>';
  if($page>6) echo ' .. ';
  $r=3;
  if($totalpages>1) for($pl=$page-$r;$pl<$page+$r+1;$pl++) if($pl>0 && $pl<=$totalpages) {
?>
  <li><a href="<?=router::url()?>?page=<?=$pl?>"><?=($pl)?></a></li>
<?php }
  if($page<$totalpages-6) echo ' .. ';
  if($page<$totalpages-5) echo '<li><a href="'.router::url().'?page='.$totalpages.'">'.$totalpages.'</a></li>';
?>
</ul>
