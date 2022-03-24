<?=View::script('lib/bootstrap5/bootstrap.bundle.min.js')?>
<?=View::cssAsync('lib/bootstrap5/bootstrap.min.css')?>
<?php
if (Form::posted('contact-form'.$widget_data->widget_id) && Event::get('recaptcha', true)) {
  new Sendmail(["post"=>["name","email","message"]]);
  View::alert('success', htmlentities($widget_data->success_msg));
}
?>
<section>
<div class="container">
<form method="post" action="<?=$_SERVER['REQUEST_URI']?>">
  <?=Form::hiddenInput('contact-form'.$widget_data->widget_id)?>
  <?php View::alerts() ?>
  <div class="mb-3">
    <label><?=__("Name")?></label>
    <input name="name" class="form-control g-input" required/>
  </div>
  <div class="mb-3">
    <label><?=__("E-mail")?></label>
    <input name="email" class="form-control g-input" type="email" required/>
  </div>
  <div class="mb-3">
    <label><?=__("Subject")?></label>
    <textarea name="message" class="form-control g-input" required></textarea>
  </div>
  <?php Event::fire('recaptcha.form')?>
  <input type="submit" class="btn btn-primary w-100 text-white" value="<?=__('Send')?>">
</form>
</div>
</section>
