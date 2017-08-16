<?php

event::listen('body',function(){
    $shortname = gila::option('gila_disqus.shortname');
  ?>
  <script id="dsq-count-scr" src="//<?=$shortname?>.disqus.com/count.js" async></script>
  <?php
});


event::listen('post.after',function(){
	$href = gila::config('base').router::$url;
	$shortname = gila::option('gila_disqus.shortname');
	?>
	<div id="disqus_thread"></div>
	<script>

	/*var disqus_config = function () {
	this.page.url = '<?=$href?>';
	this.page.identifier = <?=router::get('id',1)?>;
	};*/

	(function() {
	var d = document, s = d.createElement('script');
	s.src = 'https://<?=$shortname?>.disqus.com/embed.js';
	s.setAttribute('data-timestamp', +new Date());
	(d.head || d.body).appendChild(s);
	})();
	</script>
	<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
	<?php
});
