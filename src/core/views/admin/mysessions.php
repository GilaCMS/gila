<style>
.device-pill{background: var(--main-a-color); border-radius:6px; padding:8px; display:inline-block;margin:8px;opacity:0.8}
.device-pill .close {cursor:pointer}
.device-pill.selected {opacity:1}
.device-pill img {width:20px}
</style>
<?=View::script('lib/vue/vue.min.js')?>
<?=View::script('core/admin/vue-components.js')?>
<?=View::script('core/admin/media.js')?>

<div class="row">
  <?php
  $sessions = Gila\Session::findByUserId(Gila\Session::userId());
  $info = [];
  foreach ($sessions as $key=>$session) {
    $user_agent = $session['user_agent'];
    $info[$key] = Gila\UserAgent::info($user_agent);
    $info[$key]['ip'] = $session['ip_address'];
    if ($_COOKIE['GSESSIONID']==$session['gsessionid']) {
      $info[$key]['current'] = true;
    }
  }
  ?>
<div class="wrapper" id="currentDevices">
  <h3><?=__('You are connected with these devices', ['es'=>'Estas conectado con esos dispositivos'])?></h3>
  <div v-for="(s,i) in sessions" v-bind:class="{'device-pill':true,selected:s.current}">
    <img :src="'src/core/assets/'+iconFile(s.device)">
    {{s.os}} | {{s.browser}} | IP: {{s.ip}}
    <span v-if="s.current"></span>
    <span v-else class="close" @click="removeDevice(i)">&times;</span>
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
