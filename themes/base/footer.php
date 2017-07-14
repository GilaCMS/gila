
<hr>

<footer class="wrapper">
    <p class="copyright text-muted">Copyright &copy; Your Website 2016</p>
<?php
global $starttime;
$end = microtime(true);
$creationtime = ($end - $starttime);
printf("<p>Page created in %.6f seconds.</p>", $creationtime);
?>
</footer>

</body>

</html>
