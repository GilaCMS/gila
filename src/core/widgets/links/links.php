<ul>
	<?php foreach(json_decode($widget_data->links) as $link) { ?>
	<li><a href="<?=$link[1]?>"><?=$link[0]?></a>
	<?php } ?>
</ul>
