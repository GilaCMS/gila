<style>
.device-pill{background: var(--main-a-color); border-radius:6px; padding:8px; display:inline-block;margin:8px;opacity:0.8}
.device-pill .close {cursor:pointer}
.device-pill.selected {opacity:1}
#main-wrapper>div{background: inherit !important;border:none}
#profile-photo>div{margin:auto}
</style>
<?=View::script('lib/vue/vue.min.js')?>
<?=View::script('core/admin/vue-components.js')?>
<?=View::script('core/vue-upload-media.js')?>
<?=View::script('core/admin/media.js')?>

<?php View::alerts(); ?>

<div class="gm-grid">

<div>
  <div id="profile-forms" class="g-card" style="padding:0 16px;background:white">
    <form method="post" action="admin/profile" class="g-form">
    <h2 class="text-align-center"><?=__('Personal Information')?></h2>
    <br>
    <div id="profile-photo" class="text-align-center">
      <input-upload-media name="gila_photo" value="<?=$user_photo?>"/>
    </div>

    <br><div>
    <label class="gm-6"><?=__('Name')?></label>
    <input name="gila_username" value="<?=Session::key('user_name')?>" class="gm-6" />
    </div>

    <br><div>
    <label class="gm-6"><?=__('Email')?></label>
    <input disabled value="<?=Session::key('user_email')?>" class="gm-6" />
    </div>

    <br><div>
    <label class="gm-6"><?=__('Twitter Account')?></label>
    <input name='twitter_account' value="<?=$twitter_account?>" class="gm-6" />
    </div>

    <br><div>
      <button type="submit" name="submit-btn" onclick="this.value='submited'"
      class="btn btn-primary"><?=__('Update Profile')?></button>
    </div>

    <p><?=__('Permissions')?>:
    <ul><?php
    foreach(Gila\Profile::getPermissions(Session::userId()) as $per) {
      echo '<li>'.$per;
    }
    ?></ul></p>

    </form>
  </div>
</div>

<div>
  <div id="profile-forms" class="g-card" style="padding:0 16px;background:white">
    <form method="post" action="admin/profile" class="g-form">
    <h2 class="text-align-center"><?=__('Security')?></h2>
    <br><div>
    <label class="gm-6"><?=('Password')?></label>
    <input name="old_pass" type="password" class="gm-6" />
    </div>

    <br><div>
    <label class="gm-6"><?=__('New Password')?></label>
    <input name="new_pass" type="password" class="gm-6" />
    </div>

    <br><div>
    <label class="gm-6"><?=__('Confirm Password')?></label>
    <input name="new_pass2" type="password" class="gm-6" />
    </div>

    <br><div>
      <button type="submit" name="submit-btn" onclick="this.value='password'"
      class="btn btn-primary"><?=__('Change Password')?></button>
    </div>
    </form>

    <form method="post" action="admin/profile" class="g-form">

    <br><div>
    <label class="gm-6"><?=__('Unique Token Key')?></label>
    <input readonly type="text" value="<?=$token?>" class="gm-6" />
    </div>

    <br><div>
    <button type="submit" name="token" value="generate"
    class="btn btn-primary"><?=__('Generate Token Key')?></button>
    <button type="submit" name="token" value="delete"
    class="btn btn-primary"><?=__('Delete Token Key')?></button>
    </div>

    </form>
  </div>
</div>

<script>
var profileForms = new Vue({
  el: '#profile-forms'
});
</script>

</div>
