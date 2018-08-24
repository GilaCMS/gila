
</div>
</div>

<footer  class="fullwidth pad"  style="">
  <div style="max-width:900px; margin:auto">
    <div class="footer-widget">
        <?php view::widget_area('foot')?>
    </div>
    <p class="copyright footer-text">
        <?=gila::option('theme.footer-text','Copyright &copy; Your Website 2017');?>
        <span style="float:right">Powered by <a href="http://gilacms.com" target="_blank">Gila CMS</a></span>
    </p>
  </div>
</footer>
<script src="src/core/assets/lazyImgLoad.js" async></script>
<?php event::fire('footer')?>
</body>
<div class="pad">
<?php
global $starttime;
$end = microtime(true);
$creationtime = ($end - $starttime);
printf("<br>Page created in %.6f seconds.", $creationtime);
echo "<br>Gila CMS version ".$GLOBALS['version'];
echo " <a href='https://twitter.com/GilaCms' target='_blank' rel='noopener noreferrer'><i class='fa fa-twitter'></i></a>"
?>
</div>
</html>
