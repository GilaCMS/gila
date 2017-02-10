</section>

<section id="footer_top_area">
    <div class="clearfix wrapper footer_top">
        <div class="clearfix footer_top_container">
            <div class="clearfix single_footer_top floatleft">
                <?php view::block('footer'); ?>
            </div>
            <div class="clearfix single_footer_top floatleft">
                <?php view::block('footer'); ?>
            </div>
            <div class="clearfix single_footer_top floatleft">
                <?php view::block('footer'); ?>
                <h2>Usefull Links</h2>
                <ul>
                    <li><a href="">Important links of this site</a></li>
                    <li><a href="">Important links of this site</a></li>
                    <li><a href="">Important links of this site</a></li>
                    <li><a href="">Important links of this site</a></li>
                    <li><a href="">Important links of this site</a></li>
                    <li><a href="">Important links of this site</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section id="footer_bottom_area">
    <div class="clearfix wrapper footer_bottom">
        <div class="clearfix copyright floatleft">
            <p> Copyright &copy; All rights reserved by <span>Wpfreeware.com</span></p>
            <?php
            global $starttime;
            $end = microtime(true);
            $creationtime = ($end - $starttime);
            printf("<br>Page created in %.6f seconds.", $creationtime);
            ?>
        </div>
        <div class="clearfix social floatright">
            <ul>
                <li><a class="tooltip" title="Facebook" href=""><i class="fa fa-facebook-square"></i></a></li>
                <li><a class="tooltip" title="Twitter" href=""><i class="fa fa-twitter-square"></i></a></li>
                <li><a class="tooltip" title="Google+" href=""><i class="fa fa-google-plus-square"></i></a></li>
                <li><a class="tooltip" title="LinkedIn" href=""><i class="fa fa-linkedin-square"></i></a></li>
                <li><a class="tooltip" title="tumblr" href=""><i class="fa fa-tumblr-square"></i></a></li>
                <li><a class="tooltip" title="Pinterest" href=""><i class="fa fa-pinterest-square"></i></a></li>
                <li><a class="tooltip" title="RSS Feed" href=""><i class="fa fa-rss-square"></i></a></li>
                <li><a class="tooltip" title="Sitemap" href=""><i class="fa fa-sitemap"></i> </a></li>
            </ul>
        </div>
    </div>
</section>

<script type="text/javascript" src="libs/jquery/jquery-2.2.4.min.js"></script>
<!--script type="text/javascript" src="http://code.jquery.com/jquery-1.7.0.min.js"></script-->
<script type="text/javascript" src="themes/yellow-blog/js/jquery.tooltipster.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.tooltip').tooltipster();
    });
</script>
 <script type="text/javascript" src="themes/yellow-blog/js/selectnav.min.js"></script>
<script type="text/javascript">
    selectnav('nav', {
      label: '-Navigation-',
      nested: true,
      indent: '-'
    });
</script>
<script src="themes/yellow-blog/js/pgwslider.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.pgwSlider').pgwSlider({
            intervalDuration: 10000
        });
    });
</script>

<!--
---- Clean html template by http://WpFreeware.com
---- This is the main file (index.html).
---- You are allowed to change anything you like. Find out more Awesome Templates @ wpfreeware.com
---- Read License-readme.txt file to learn more.
-->

</body>
</html>
