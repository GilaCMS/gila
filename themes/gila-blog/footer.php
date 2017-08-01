

<footer class="wrapper" style="background:#c6c6c6;margin-top:10px">
    <p class="copyright text-muted">Copyright &copy; Your Website 2016</p>
<?php
global $starttime;
$end = microtime(true);
$creationtime = ($end - $starttime);
//printf("<p>Page created in %.6f seconds.</p>", $creationtime);
?>
</footer>

</div>
</body>

</html>
