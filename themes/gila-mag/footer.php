
    </div>
    <footer class="wrapper" style="">
      <div class="footer-widget" style="display: grid;
grid-template-columns: repeat(auto-fit,minmax(300px,1fr));">
        <?php View::widgetArea('footer')?>
      </div>
      <p class="copyright footer-text">
        <?=Config::option('theme.footer-text','Copyright &copy; Your Website '.date('Y'));?>
        <span style="float:right"><a href="http://gilacms.com" target="_blank">Gila CMS</a></span>
      </p>
    </footer>

  </div>
  <?=View::script('core/gila.min.js');?>
</body>

</html>
