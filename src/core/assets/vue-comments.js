Vue.component('input-comments', {
  template: '<div style="background:#a6d9ff;max-height:220px;overflow-y:scroll;padding:4px">\
  <input v-model="value" type="hidden">\
  <div v-for="(comm,i) in comments" style="border-radius: 4px;padding: 4px;margin-bottom:4px">\
  <span style="font-size:80%;font-weight:bold">{{comm.sender}}</span>\
  <span style="font-size:80%;font-weight:bold;float:right">{{dateFormat(comm.created)}}</span>\
  <p style="margin:0;">{{comm.text}}</p>\
  </div>\
  <textarea v-model="comment" placeholder="..." style="resize:vertical;width:100%;background:#cfefff;border:0"></textarea>\
  <div v-if="content"><button style="padding:4px" type="button" @click="saveComment()">Enviar</button></div>\
  </div>\
',
  props: ['name','value','form','fieldname','username'],
  data: function() {
    content = null
    id= null
    if(this.form) {
      f = document.getElementById(this.form)
      content = f.getAttribute('data-table')
      id = f.getAttribute('data-id')
    }
    if(this.value=='') this.value='[]'
    return {
      field: [],
      comment: '',
      comments: JSON.parse(this.value),
      content: content,
      id: id
    }
  },
  methods:{
    idByName: function() {
      id = this.name.replace("[", "_");
      return id.replace("]", "_")
    },
    dateFormat: function(date) {
      d = new Date(date)
      hours = d.getHours()
      if(hours<10) hours = '0'+hours
      min = d.getMinutes()
      if(min<10) min = '0'+min
      return d.getDate()+'/'+(d.getMonth()+1)+'/'+(d.getYear()+1900)+' '+hours+':'+min
    },
    saveComment: function() {
      this.comments.push({sender:this.username,text:this.comment,created:new Date()})
      this.comment = ''
      if(this.content) g.post('cm/update_rows?t='+content+'&id='+this.id, 'id='+this.id+'&'+this.fieldname+'='+JSON.stringify(this.comments), function(){
      //if(this.content) g.post('cm/update_rows?t=comments&content_id='+this.id+'&content='+this.content, 'comment='+JSON.stringify(this.comments), function(){

      })
    }
  },
  mounted: function() {
    this.$el.scrollTop = this.$el.clientHeight
  }
})
