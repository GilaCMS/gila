<?php
if (gForm::posted('contact-form'.$widget_data->widget_id) && event::get('recaptcha',true)) {
  new sendmail(["post"=>["name","email","subject"]]);
  view::alert('success', $widget_data->success_msg);
}
?>

<form role="form" method="post" action="<?=$_SERVER['REQUEST_URI']?>" class="g-form">
  <?=gForm::hiddenInput('contact-form'.$widget_data->widget_id)?>
  <?php view::alerts() ?>
  <label><?=__("Name")?></label>
  <input name="name" class="form-control g-input" autofocus required/>
  <label><?=__("E-mail")?></label>
  <input name="email" class="form-control g-input" required/>
  <label><?=__("Subject")?></label>
  <textarea name="message" class="form-control g-input" required></textarea>
  <?php event::fire('recaptcha.form')?>
  <input type="submit" class="btn btn-primary btn-block" value="<?=__('Send')?>">
</form>
