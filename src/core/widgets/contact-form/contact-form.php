<?php
if (Form::posted('contact-form'.$widget_data->widget_id) && Event::get('recaptcha', true)) {
  new Sendmail(["post"=>["name","email","message"]]);
  View::alert('success', htmlentities($widget_data->success_msg));
}
?>
<section>
<div class="container">
<form role="form" method="post" action="<?=$_SERVER['REQUEST_URI']?>" class="g-form">
  <?=Form::hiddenInput('contact-form'.$widget_data->widget_id)?>
  <?php View::alerts() ?>
  <label><?=__("Name")?></label>
  <input name="name" class="form-control g-input" required/>
  <label><?=__("E-mail")?></label>
  <input name="email" class="form-control g-input" type="email" required/>
  <label><?=__("Subject")?></label>
  <textarea name="message" class="form-control g-input" required></textarea>
  <?php Event::fire('recaptcha.form')?>
  <input type="submit" class="btn g-btn btn-primary btn-block" value="<?=__('Send')?>">
</form>
</div>
</section>
