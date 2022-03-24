<?=View::script('lib/bootstrap5/bootstrap.bundle.min.js')?>
<?=View::cssAsync('lib/bootstrap5/bootstrap.min.css')?>
<div id="MyCarousel" class="carousel slide <?= ($data['carousel-full-width']=='1')?'position-absolute w-100 start-0':'' ?>" data-bs-ride="carousel">
  <div class="carousel-indicators">
    <?php if (isset($data['items'])) {
      foreach (json_decode($data['items']) as $i => $item) { ?>
        <button type="button" data-bs-target="#MyCarousel" data-bs-slide-to="<?=$i?>" <?= $i==0?'class="active"':'' ?> aria-current="true" aria-label="Slide <?=$i+1?>"></button>
      <?php }
    }?>
  </div>
  <div class="carousel-inner">
    <?php if (isset($data['items'])) {
      foreach (json_decode($data['items']) as $i => $item) { ?>
        <div class="carousel-item <?= $i==0?'active':'' ?>" data-bs-interval="<?=( (!$data['duration-in-secons']=='')?intval($data['duration-in-secons'])*1000:5000) ?>">
          <div class="d-block w-100" style="height:<?=(isset($data['carousel-size'])?intval($data['carousel-size'])*10:50).'vh'?>; background-image: url(<?=htmlentities(View::thumb($item[0], 300))?>); background-repeat: no-repeat; background-size:cover;"> </div>
          <div class="carousel-caption d-none d-md-block <?=( isset($data['text-align'])?'text-'.$data['text-align']:'') ?>" style="height: <?=( isset($data['vertical-align'])?(($data['vertical-align'])*10).'%':'') ?>">
            <?php if (isset($item[1])) { ?>
              <h5 style="<?= isset($data['color-text'])?'color:'.$data['color-text']:'color:white' ?>;font-size:<?= (($data['title-font-size'])?$data['title-font-size'].'px':'40px') ?>" ><?= $item[1] ?></h5>
            <?php } ?>
            <?php if (isset($item[2])) { ?>
              <p style="<?= isset($data['color-text'])?'color:'.$data['color-text']:'color:white' ?>;font-size:<?= (($data['description-font-size'])?$data['description-font-size'].'px':'25px') ?>"><?= $item[2] ?></p>
            <?php } ?>
            <?php if (!($item[3])=='') { ?>
              <a class="btn btn-success" style="font-size:<?= (isset($data['button-font-size'])?$data['button-font-size'].'px':'24px') ?>" href="<?= $item[3] ?>"><?= $data['button-title'] ?></a>
            <?php } ?>
          </div>
        </div>
      <?php }
    }?>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#MyCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#MyCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>

<?php if ($data['carousel-full-width']=='1') { ?>
  <div style="height:<?=(isset($data['carousel-size'])?intval($data['carousel-size'])*10+10:60).'vh'?>" class="position-relative mx-5"></div>
<?php } ?>
