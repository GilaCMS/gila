

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
  template: '<div class="pointer:hover shadow:hover;" \
  style="background:var(--main-input-color);width:160px;height:160px;max-width:100%;max-height:100%;display: grid;\
  justify-content:center; align-content:center; position:relative;min-width:50px;" \
  :onclick="\'open_media_gallery(\\\'#imd\'+idByName()+\'\\\')\'">\
<img v-if="!value" src="assets/core/camera.svg" style="width:50px;margin:auto">\
<img v-if="value" :src="\'lzld/thumb?media_thumb=160&src=\'+value" style="max-width:100%;margin:auto">\
<svg v-if="value" height="28" width="28" @click.stop="value=null;return false;"\
style="position:absolute;right:0;top:0" viewBox="0 0 28 28">\
  <circle cx="14" cy="14" r="10" stroke-width="0" fill="#666"></circle>\
  <line x1="9" y1="9" x2="18" y2="18" style="stroke:#fff;stroke-width:3"></line>\
  <line x1="9" y1="18" x2="18" y2="9" style="stroke:#fff;stroke-width:3"></line>\
</svg>\
<input v-model="value" type="hidden" :id="\'imd\'+idByName()" :name="name">\
</div>\
',
  props: ['name','value','fieldset'],
  data: function() {
    return {
      field: []
    }
  },
  methods:{
    idByName: function() {
      id = this.name.replace("[", "_");
      return id.replace("]", "_")
    },
    beforeCreate: function() {
      if(typeof this.fieldset!=='undefined') {
        this.field = JSON.parse(this.fieldset)
      }
    }
  }
})


Vue.component('input-gallery', {
  template: '<div style="display: grid; gap: 1em; width: 100%;\
  grid-template-columns: repeat(auto-fit,minmax(50px,120px));\
  grid-template-rows: repeat(auto-fit, 120px);">\
  <input-media v-for="(src,i) in sources" :value="src" :name="name+\'[\'+i+\']\'">\
</div>',
  props: ['name','value'],
  data: function() {
    return {
      sources: JSON.parse(this.value)
    }
  }
})


Vue.component('g-multiselect', {
  template: '<div :id="\'gm-\'+name+value" style="padding:var(--main-padding);min-width:180px;cursor:pointer;\
  background:var(--main-input-color);position:relative" @click="dropdown=!dropdown">\
  <span v-if="values==[]">{{placeholder}}</span>\
  <span v-for="(value,i) in values" @click="toggle(value)"\
  style="color:white;background:var(--main-primary-color);margin-right:4px;padding:4px;border-radius:4px">\
  &times; {{opList[value]}}</span>&nbsp;\
  <div v-if="dropdown" style="position:absolute; min-width:160px; padding:0;\
  margin:12px -12px;border:1px solid lightgrey; z-index:1;\
  background:white;">\
  <div style="float:right;font-size:150%;margin:0 4px" @click.stop="dropdown=false">&times;</div>\
  <div v-for="(op,i) in opList" style="padding:6px" @click="toggle(i)" v-html="optionDisplay(op,i)"></div>\
  </div>\
  <input v-for="(v,i) in values" type="hidden" :value="v" :name="name+\'[\'+i+\']\'">\
</div>',
  props: ['name','value','options','placeholder'],
  data: function() {
    return {
      values: JSON.parse(this.value)??[],
      placeholder: this.placeholder??'xexexe',
      opList: JSON.parse(this.options),
      dropdown: false
    }
  },
  methods: {
    toggle: function(i) {
      var index = this.values.indexOf(i);
      if (index === -1) this.values.push(i); else this.values.splice(index, 1);
      this.dropdown=false
    },
    optionDisplay: function(op,i) {
      var index = this.values.indexOf(i);
      if (index !== -1) op = op+" &#10003;";
      return op
    }
  }
})
