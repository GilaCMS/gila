<style>
.device-pill{background: var(--main-a-color); border-radius:6px; padding:8px; display:inline-block;margin:8px;opacity:0.8}
.device-pill .close {cursor:pointer}
.device-pill.selected {opacity:1}
</style>
<?=View::script('lib/vue/vue.min.js')?>
<?=View::script('core/admin/vue-components.js')?>
<?=View::script('core/admin/media.js')?>

<div class="row">
  <?php
  $sessions = Gila\User::metaList(Session::userId(), 'GSESSIONID');
  $info = [];
  foreach ($sessions as $key=>$session) {
    if (file_exists(LOG_PATH.'/sessions/'.$session)) {
      $user_agent = json_decode(file_get_contents(LOG_PATH.'/sessions/'.$session))->user_agent;
      $info[$key] = UserAgent::info($user_agent);
      if ($_COOKIE['GSESSIONID']==$session) {
        $info[$key]['current'] = true;
      }
    } else {
      Gila\User::metaDelete(Session::userId(), 'GSESSIONID', $session);
    }
  }
  ?>
<div class="wrapper" id="currentDevices">
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
