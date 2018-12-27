<form role="form" method="post" action="<?=$widget_data->url?>" class="g-form wrapper g-card">
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
    <?php event::fire('recapcha.form')?>
    <input type="submit" class="btn btn-primary btn-block" value="Send">
</form>
