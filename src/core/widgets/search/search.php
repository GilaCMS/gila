<aside class="widget-search">
<?=$data['text']?>
<form method="get" class="" action="<?=Config::base('blog')?>" style="position:relative;margin:0 6px">
  <input name="search" class="g-input fullwidth" value="<?=htmlentities($_GET['search']??'')?>" onkeypress="if(event.keyCode==13) submit()">
  <svg height="24" width="24" style="position:absolute;right:8px;top:8px" stroke="#929292" stroke-width="3" viewBox="0 0 28 28">
    <circle cx="12" cy="12" r="8" fill="none"></circle>
    <line x1="17" y1="17" x2="24" y2="24"></line>
  </svg>
</form>
</aside>
