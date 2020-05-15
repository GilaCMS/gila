</div>
</div>

<footer  class="fullwidth pad"  style="">
  <div style="max-width:900px; margin:auto">
    <div class="footer-widget">
        <?php View::widgetArea('foot')?>
    </div>
    <p class="copyright footer-text">
        <?=Gila::option('theme.footer-text','Copyright &copy; Your Website '.date('Y'));?>
        <span style="float:right">Powered by <a href="http://gilacms.com" target="_blank">Gila CMS</a></span>
    </p>
  </div>
</footer>
<?php View::scriptAsync("core/lazyImgLoad.js")?>
<?php Event::fire('footer')?>
</body>

</html>
