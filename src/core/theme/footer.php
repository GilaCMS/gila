
</div>
</div>
</div>
<?php
global $starttime;
$end = microtime(true);
$creationtime = ($end - $starttime);
printf("<br>Page created in %.6f seconds.", $creationtime);
?>
</div>
<!-- /#page-content-wrapper -->

</div>
<!-- /#wrapper -->

<!-- jQuery -->
<script src="libs/jquery/jquery-2.2.4.min.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="libs/bootstrap/bootstrap.min.js"></script>

<!-- Menu Toggle Script -->
<script>
$("#menu-toggle").click(function(e) {
e.preventDefault();
$("#wrapper").toggleClass("toggled");
});
</script>

</body>

</html>
