</div>
</div>

<footer  class="fullwidth pad"  style="">
  <div style="max-width:900px; margin:auto">
    <div class="footer-widget">
        <?php View::widget_area('foot')?>
    </div>
    <p class="copyright footer-text">
        <?=Gila::option('theme.footer-text','Copyright &copy; Your Website '.date('Y'));?>
        <span style="float:right">Powered by <a href="http://gilacms.com" target="_blank">Gila CMS</a></span>
    </p>
  </div>
</footer>
<script src="src/core/assets/lazyImgLoad.js" async></script>
<?php Event::fire('footer')?>
</body>

</html>
