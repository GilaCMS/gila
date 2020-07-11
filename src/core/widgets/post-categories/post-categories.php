<ul>
	<?php if ($list=core\models\Post::categories()) {
  foreach ($list as $link) { ?>
	<li><a href="<?=Gila::url('blog/category/'.$link[0])?>"><?=$link[1]?></a>
	<?php }
} ?>
</ul>
