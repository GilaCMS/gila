

Vue.component('input-list', {
    template: '<div>\
<div v-for="(row,key) in pos">\
<span v-for="(field,fkey) in fields">\
	<span v-if="field==&quot;image&quot;" style="width:50px" >\
		<img :src="imgSrc(pos[key][fkey])"  :onclick="\'open_media_gallery(\\\'#il\'+field+key+\'\\\')\'" style="width:50px;height:50px;vertical-align:middle" />\
		<input v-model="pos[key][fkey]" type="hidden" :id="\'il\'+field+key" @input="update">\
	</span>\
	<input v-if="field!=&quot;image&quot;" v-model="pos[key][fkey]" :id="\'il\'+field+fkey" @input="update" :placeholder="field.toUpperCase()">\
</span>\
&nbsp;<span @click="removeEl(key)" class="btn btn-error">-</span>\
</div>\
<a @click="add()" class="btn btn-success">+</a>\
<input v-model="ivalue" type="hidden" :name="name" >\
</div>\
',
    props: ['name','value','fieldset'],
    data: function(){ return {
    pos: JSON.parse(this.value),
    fields: JSON.parse(this.fieldset),
    ivalue: this.value
    }
  },
  methods:{
    add: function(){
    array = new Array()
    for(i in this.fields) if(this.fields[i]=='image') {
      array[i] = 'src/core/assets/photo.png'
    } else array[i] = ''
        this.pos.push(array)
        this.update()
    },
    removeEl: function(index){
      	this.pos.splice(index,1)
      	this.update()
    },
    imgSrc: function(src) {
      if(src.split('.').pop()=='svg') {
        return src;
      }
      return 'lzld/thumb?src='+src;
    },
    update: function(){
      this.ivalue = JSON.stringify(this.pos)
    },
    beforeCreate: function(){
      this.pos=JSON.parse(this.value)
      this.fields=JSON.parse(this.fieldset)
      this.ivalue = this.value
    }
  }
})

Vue.component('input-media', {
  template: '<div style="width:200px;height:200px">\
<img :src="\'lzld/thumb?src=\'+value" style="max-width:100%">\
</div>\
',
  props: ['name','value'],
  //data: function() {
  //  return {
  //    pos: JSON.parse(this.value),
  //    fields: JSON.parse(this.fieldset),
  //    ivalue: this.value
  //  }
  //},
  methods:{
    update: function(){
      //this.ivalue = JSON.stringify(this.pos)
    },
    beforeCreate: function(){
      //this.pos=JSON.parse(this.value)
      //this.fields=JSON.parse(this.fieldset)
      //this.ivalue = this.value
    }
  }
})
