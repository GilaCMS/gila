<!-- Post Header -->
<!-- Set your background image for this header on the line below. -->
<div class="wrapper">
  <h1><?=$title?></h1>

  <div>
      <?=nl2br($text)?>
  </div>

  <form role="form" method="post" action="" class="g-form wrapper g-card">
      <label>Name</label>
      <div class="form-group">
          <input class="form-control fullwidth" placeholder="name" name="name" autofocus required>
      </div>
      <label>E-mail</label>
      <div class="form-group">
          <input class="form-control fullwidth" placeholder="E-mail" name="email" type="email" required>
      </div>
      <label>Subject</label>
      <div class="form-group ">
          <input class="form-control fullwidth" placeholder="Password" name="password" type="password" value="" required>
      </div>
      <?php event::fire('recaptcha.form')?>
      <input type="submit" class="btn btn-primary btn-block" value="Send">
  </form>

</div>
