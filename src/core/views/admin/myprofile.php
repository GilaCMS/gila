<style>
.device-pill{background: var(--main-a-color); border-radius:6px; padding:8px; display:inline-block;margin:8px;opacity:0.8}
.device-pill .close {cursor:pointer}
.device-pill.selected {opacity:1}
</style>
<?=View::script('lib/vue/vue.min.js')?>

<div class="row">
<div class="gm-6">
    <?php View::alerts(); ?>
    <form method="post" action="admin/profile" class="g-form">
    <fieldset>
    <br><div class="gm-12 row">
    <label class="gm-6"><?=__('Name')?></label>
    <input name="gila_username" value="<?=Session::key('user_name')?>" class="gm-6" />
    </div>

    <br><div class="gm-12 row">
    <label class="gm-6"><?=__('Email')?></label>
    <input disabled value="<?=Session::key('user_email')?>" class="gm-6" />
    </div>

    <br><div class="gm-12 row">
    <label class="gm-6"><?=__('Twitter Account')?></label>
    <input name='twitter_account' value="<?=$twitter_account?>" class="gm-6" />
    </div>

    <br><button type="submit" name="submit-btn" onclick="this.value='submited'"
    class="btn btn-primary"><?=__('Update Profile')?></button>
    </fieldset>
    </form>

    <form method="post" action="admin/profile" class="g-form">
    <fieldset>
    <br><div class="gm-12 row">
    <label class="gm-6"><?=('Password')?></label>
    <input name="old_pass" type="password" class="gm-6" />
    </div>

    <br><div class="gm-12 row">
    <label class="gm-6"><?=__('New Password')?></label>
    <input name="new_pass" type="password" class="gm-6" />
    </div>

    <br><div class="gm-12 row">
    <label class="gm-6"><?=__('Confirm Password')?></label>
    <input name="new_pass2" type="password" class="gm-6" />
    </div>

    <br><button type="submit" name="submit-btn" onclick="this.value='password'"
    class="btn btn-primary"><?=__('Change Password')?></button>
    </fieldset>
    </form>

    <form method="post" action="admin/profile" class="g-form">
    <fieldset>
    <br><div class="gm-12 row">
    <label class="gm-6"><?=__('Unique Token Key')?></label>
    <input readonly type="text" value="<?=$token?>" class="gm-6" />
    </div>

    <br><div class="gm-12">
    <button type="submit" name="token" value="generate"
    class="btn btn-primary gm-4"><?=__('Generate Token Key')?></button>
    <button type="submit" name="token" value="delete"
    class="btn btn-primary gm-4"><?=__('Delete Token Key')?></button>
    </div>

    </fieldset>
    </form>
</div>

  <?php
  $sessions = core\models\User::metaList(Session::userId(), 'GSESSIONID');
  $info = [];
  foreach($sessions as $key=>$session) if(file_exists(LOG_PATH.'/sessions/'.$session)) {
    $user_agent = json_decode(file_get_contents(LOG_PATH.'/sessions/'.$session))->user_agent;
    $info[$key] = UserAgent::info($user_agent);
    if($_COOKIE['GSESSIONID']==$session) $info[$key]['current'] = true;
  }
  ?>
<div class="gm-6 wrapper" id="currentDevices">
  <h3>You are connected with these devices</h3>
  <div v-for="(s,i) in sessions" v-bind:class="{'device-pill':true,selected:s.current}">
    <img :src="'src/core/assets/'+iconFile(s.device)">{{s.os}} | {{s.browser}}
    <span v-if="s.current">(this)</span>
    <i v-else class="close" @click="removeDevice(i)">&times;</i>
  </div>
</div>

<script>
var connectedDevicesApp = new Vue({
  el: "#currentDevices",
  data: {
    sessions: <?=(json_encode($info)??'[]')?>
  },
  methods: {
    removeDevice: function (x) {
      if(confirm("Disconnect from this device?")) {
        _this = this
        g.post('admin/deviceLogout', 'device='+x, function(data) {
          data = JSON.parse(data)
          if(data.error) {
            alert(data.error);
          } else {
            alert("Your account now is disconnected from that device");
            _this.sessions = data
          }
        })
      }
    },
    iconFile: function(device) {
      if(device=='MOBILE') return 'mobile.svg';
      return 'monitor-o.svg';
    }
  }
})

</script>

</div>
