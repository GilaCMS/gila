
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
  wrapper_toggle();
});
g.swipe('#wrapper', 'left', function(){
  this.classList.remove('toggled');
  wrapper_toggle();
})
g.swipe('#wrapper', 'right', function(){
  this.classList.add('toggled');
  wrapper_toggle();
})
function wrapper_toggle() {
  value = document.getElementById("wrapper").classList[0]=='toggled'?true:false
  document.cookie = 'sidebar_toggled='+value+';path=/;SameSite=Lax;';
  setTimeout(g.lazyLoad, 100);
}
</script>

</body>
</html>
