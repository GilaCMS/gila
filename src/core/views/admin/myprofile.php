
<div class="gm-12">
    <?php view::alerts(); ?>
    <form method="post" action="admin/profile" class="g-form">

    <br><div class="gm-12">
    <label class="gm-3">Name</label>
    <input name="gila_username" value="<?=session::key('user_name')?>" class="gm-3" />
    </div>

    <br><div class="gm-12">
    <label class="gm-3">Email</label>
    <input disabled value="<?=session::key('user_email')?>" class="gm-3" />
    </div>

    <br><div class="gm-12">
    <label class="gm-3">Twitter Account</label>
    <input name='twitter_account' value="<?=$c->twitter_account?>" class="gm-3" />
    </div>

    <br><input type="submit" name="submit-btn" onclick="this.value='submited'"
    class="btn btn-primary col-md-1 col-md-offset-1 gm-1"  />
    </form>
</div>
