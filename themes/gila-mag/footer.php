
    </div>
    <footer class="wrapper" style="">
      <div class="footer-widget">
        <?php View::widgetArea('foot')?>
      </div>
      <p class="copyright footer-text">
        <?=Config::option('theme.footer-text','Copyright &copy; Your Website '.date('Y'));?>
        <span style="float:right"><a href="http://gilacms.com" target="_blank">Gila CMS</a></span>
      </p>
      <?php Event::fire('footer')?>
    </footer>

  </div>
</body>

</html>
