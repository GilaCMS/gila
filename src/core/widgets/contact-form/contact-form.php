<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && event::get('recaptcha',true)
    && $_POST['wdgtid']==$widget_data->widget_id) {
  new sendmail(["post"=>["name","email","subject"]]);
  view::alert('success', $widget_data->success_msg);
}
?>

<form role="form" method="post" action="<?=$_SERVER['REQUEST_URI']?>" class="g-form wrapper g-card">
  <input type="hidden" name="wdgtid" value="<?=$widget_data->widget_id?>">
  <?php view::alerts() ?>
  <label><?=__("Name")?></label>
  <div class="form-group">
    <input class="form-control fullwidth" name="name" autofocus required>
  </div>
  <label><?=__("E-mail")?></label>
  <div class="form-group">
    <input class="form-control fullwidth" name="email" type="email" required>
  </div>
  <label><?=__("Subject")?></label>
  <div class="form-group ">
    <textarea class="form-control fullwidth" name="subject" required></textarea>
  </div>
  <?php event::fire('recapcha.form')?>
  <input type="submit" class="btn btn-primary btn-block">
</form>
