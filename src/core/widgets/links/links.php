<ul>
	<?php if (isset($data['links'])) {
  foreach (json_decode($data['links']) as $link) { ?>
	<li><a href="<?=htmlentities($link[1])?>"><?=$link[0]?></a>
	<?php }
} ?>
</ul>
