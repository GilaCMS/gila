<ul>
	<?php if($list=core\models\post::categories()) foreach($list as $link) { ?>
	<li><a href="<?=gila::url('blog/category/'.$link[0])?>"><?=$link[1]?></a>
	<?php } ?>
</ul>
