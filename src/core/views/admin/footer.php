<script>
/*jQuery(document).on('click',"#menu-toggle",function(e) {
    e.preventDefault();
    jQuery("#wrapper").toggleClass("toggled");
});*/
document.getElementById("menu-toggle").addEventListener("click", function(e) {
    e.preventDefault();
    document.getElementById("wrapper").classList.toggle('toggled');
});
</script>

</div>

<div><hr>
<?php
global $starttime;
$end = microtime(true);
$creationtime = ($end - $starttime);
printf("<br>Page created in %.6f seconds.", $creationtime);
?>
</div>

</div>
<!-- /#wrapper -->

</body>

</html>
