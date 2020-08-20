</div>
</div>

<footer  class="fullwidth pad"  style="">
  <div style="max-width:900px; margin:auto">
    <div class="footer-widget">
        <?php View::widgetArea('footer')?>
    </div>
    <p class="copyright footer-text">
        <?=Config::option('theme.footer-text','Copyright &copy; Your Website '.date('Y'));?>
        <span style="float:right">Powered by <a href="http://gilacms.com" target="_blank">Gila CMS</a></span>
    </p>
  </div>
</footer>
<?php View::scriptAsync("core/lazyImgLoad.js")?>
</body>

</html>
