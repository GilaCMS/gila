<section id="newsSection">
  <div class="row">
    <div class="col-lg-12 col-md-12">
        <?php view::widget('latest-newsarea'); ?>
    </div>
  </div>
</section>
<?php
if($page==1) view::widget('slider');
?>
<section id="contentSection">
  <div class="row">
    <div class="col-lg-8 col-md-8 col-sm-8">
      <div class="left_content">
        <?php include 'widgets/single-post-content-left.php'; ?>

        <div class="fashion_technology_area">
            <?php include 'widgets/single-post-content.php'; ?>
            <?php include 'widgets/single-post-content.php'; ?>
        </div>

        <?php view::widget('photo-grid'); ?>

        <?php view::widget('single-post-content-left'); ?>
      </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4">
        <?php include 'sidebar.php'; ?>
    </div>
  </div>
</section>
