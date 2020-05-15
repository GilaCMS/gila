
</div>

<div class="pad">
<?php
global $starttime;
$end = microtime(true);
$creationtime = ($end - $starttime);
printf("<br>Page created in %.6f seconds.", $creationtime);
echo "<br>Gila CMS version ".Package::version('core');
echo " <a href='https://twitter.com/GilaCms' target='_blank' rel='noopener noreferrer'><i class='fa fa-twitter'></i></a>"
?>
</div>

</div><!-- /#wrapper -->

<script>
document.getElementById("menu-toggle").addEventListener("click", function(e) {
  e.preventDefault();
  document.getElementById("wrapper").classList.toggle('toggled');
  value = document.getElementById("wrapper").classList[0]=='toggled'?true:false
  document.cookie = 'sidebar_toggled='+value+';path=/;SameSite=Lax;';
  setTimeout(lazyImgLoad, 100);
});
</script>
<?php View::scriptAsync("core/lazyImgLoad.js")?>
<?php View::scripts()?>

</body>
</html>
