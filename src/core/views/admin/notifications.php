<?=View::script('lib/vue/vue.min.js');?>
<?=View::script('core/gila.min.js');?>
<style>
.readen{
  opacity:0.66
}
#notifications{
  max-width:600px;margin:auto;display:grid;gap:0.6em;
}
.unread-square.fa-square-o{
  cursor:pointer;
}
.unread-square{
  float:right;opacity:0.8;
}
</style>

<div id="notifications">
  <div style="text-align:center;color:var(--main-warning-color);"><i class="fa fa-4x fa-bell"></i></div>
<?php
$notes = Gila\UserNotification::unread($type);
?>
  <div v-for="(note,i) in notes" style="border-top:1px solid grey" :class="{readen:note.unread==0}">
    <span style="font-weight:bold;font-size:80%;float:right">{{note.created}}</span><br>
    <i v-if="note.unread==1" class="fa fa-2x fa-square-o unread-square" style="" @click="readNotification(i)"></i>
    <i v-if="note.unread==0" class="fa fa-2x fa-check-square-o unread-square"></i>
    <a v-if="note.url!=null" :href="note.url" target="_blank"><p>{{note.details}}</p></a>
    <p v-if="note.url==null">{{note.details}}</p>
    </div>
  </div>
</div>

<script>
notifications_app = new Vue({
  el: "#notifications",
  data: {
    notes: <?=json_encode($notes??[])?>
  },
  methods: {
    readNotification: function(i) {
      this.notes[i].unread=0
      g.post('lzld/notificationSetRead', {id:this.notes[i].id}, function(){
      });
    }
  }
})

</script>
