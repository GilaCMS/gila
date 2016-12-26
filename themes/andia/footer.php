</div>
<?php
global $starttime;
$end = microtime(true);
$creationtime = ($end - $starttime);
printf("<br>%.4f %.4f %.4f.", $starttime,$_SERVER['REQUEST_TIME_FLOAT'],$end);
printf("<br>Page created in %.6f seconds.", $creationtime);
?>
        <!-- Javascript -->
        <script src="libs/jquery/jquery-2.2.4.min.js"></script>
        <script src="libs/bootstrap/bootstrap.min.js"></script>
        <script src="themes/andia/assets/js/bootstrap-hover-dropdown.min.js"></script>
        <script src="themes/andia/assets/js/jquery.backstretch.min.js"></script>
        <script src="themes/andia/assets/js/wow.min.js"></script>
        <script src="themes/andia/assets/js/retina-1.1.0.min.js"></script>
        <script src="themes/andia/assets/js/jquery.magnific-popup.min.js"></script>
        <script src="themes/andia/assets/flexslider/jquery.flexslider-min.js"></script>
        <script src="themes/andia/assets/js/jflickrfeed.min.js"></script>
        <script src="themes/andia/assets/js/masonry.pkgd.min.js"></script>
        <script src="http://maps.google.com/maps/api/js?sensor=true"></script>
        <script src="themes/andia/assets/js/jquery.ui.map.min.js"></script>
        <script src="themes/andia/assets/js/scripts.js"></script>

    </body>

</html>
