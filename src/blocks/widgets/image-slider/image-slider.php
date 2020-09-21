<?=Gila\View::cssAsync('lib/glider/glider.min.css')?>
<?=Gila\View::script('lib/glider/glider.min.js')?>
<div class="myglider">
  <div> 1 </div>
  <div> 2 </div>
  <div> 3 </div>
  <div> 4 </div>
  <div> 5 </div>
  <div> 6 </div>
</div>
<div id="dots"></div>
<div class="glider-prev"></div>
<div class="glider-next"></div>
<script>new Glider(document.querySelector('.myglider'), {
  slidesToShow: 1,
  dots: '#dots',
  draggable: true,
  arrows: {
    prev: '.glider-prev',
    next: '.glider-next'
  }
});</script>