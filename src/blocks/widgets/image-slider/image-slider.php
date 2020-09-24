<?=Gila\View::css('lib/glider/glider.min.css')?>
<?=Gila\View::script('lib/glider/glider.min.js')?>
<div class="myglider">
  <div> 1 </div>
  <div> 2 </div>
  <div> 3 </div>
  <div> 4 </div>
  <div> 5 </div>
  <div> 6 </div>
</div>
<button role="button" aria-label="Previous" class="glider-prev" id="glider-prev"><i class="fa fa-chevron-left"></i></i></button>
<button role="button" aria-label="Next" class="glider-next" id="glider-next"><i class="fa fa-chevron-right"></i></i></button>
<div id="dots"></div>
<script>new Glider(document.querySelector('.myglider'), {
  slidesToShow: 1,
  dots: '#dots',
  draggable: true,
  arrows: {
    prev: '.glider-prev',
    next: '.glider-next'
  }
});</script>