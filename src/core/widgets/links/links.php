<ul>
	<?php if(isset($widget_data->links)) foreach(json_decode($widget_data->links) as $link) { ?>
	<li><a href="<?=_url($link[1])?>"><?=$link[0]?></a>
	<?php } ?>
</ul>
