

Vue.component('input-list', {
    template: '<dir style="padding:0"><table class="g-table"><tbody>\
<tr v-for="(row,key) in pos">\
<td>\
<span v-if="key>0" style="cursor:pointer;padding:0.5em 0.5em;color:black" @click="swap(key,key-1)">&uarr;</span>\
<span v-else style="padding:0.5em 0.5em;opacity:0">&uarr;</span>\
<span v-if="key<pos.length-1" style="cursor:pointer;padding:0.5em 0.5em;color:black" @click="swap(key,key+1)">&darr;</span>\
<span v-else style="padding:0.5em 0.5em;opacity:0">&darr;</span>\
</td>\
<td>\
<span v-for="(field,fkey,i) in fields">\
	<span v-if="isMedia(field)" style="width:50px" >\
		<img :src="imgSrc(pos[key][i])"  :onclick="\'open_media_gallery(\\\'#il\'+fkey+key+\'\\\')\'" style="width:50px;height:50px;vertical-align:middle" />\
		<input v-model="pos[key][i]" type="hidden" :id="\'il\'+fkey+key" @input="update">\
  </span>\
  <select v-if="isSelect(field)" v-model="pos[key][i]" @change="update">\
    <option v-for="(op,iv) in field.options" :value="iv">{{op}}</option>\
  </select>\
  <input v-if="isText(field)" v-model="pos[key][i]" @input="update"\
  :placeholder="fkey.toUpperCase()" class="g-input">\
</span>\
</td>\
<td>\
<span @click="removeEl(key)" style="cursor:pointer;padding:0.5em 0.5em;color:black">&times;</span>\
</td>\
</tr>\
</tbody></table>\
<span @click="add()" style="cursor:pointer;padding:0.5em 0.5em;color:black">+ {{addTxt()}}</span>\
<input v-model="ivalue" type="hidden" :name="name" >\
</div>\
',
  props: ['name','value','fieldlist'],
  data: function(){ 
    return {
      pos: JSON.parse(this.value),
      fields: JSON.parse(this.fieldlist),
      ivalue: this.value
    }
  },
  methods:{
    add: function(){
      array = new Array()
      for(i in this.fields) {
        if(this.isMedia(this.fields[i])) {
          array[i] = 'assets/core/photo.png'
        } else array[i] = ''
      }
      this.pos.push(array)
      this.update()
    },
    removeEl: function(index){
      this.pos.splice(index,1)
      this.update()
    },
    imgSrc: function(src) {
      if(typeof src=='undefined') return 'assets/core/photo.png'
      if(src.split('.').pop()=='svg' || src.startsWith('http:') || src.startsWith('https:')) {
        return src;
      }
      if (src.startsWith('assets/') || src.startsWith('tmp/')) {
        return src
      }
      return 'lzld/thumb?src='+src;
    },
    isMedia: function(field) {
      if(field=='image') return true
      return field.type=='media'
    },
    isText: function(field) {
      if (this.isMedia(field) || this.isSelect(field)) return false
      return true
    },
    isSelect: function(field) {
      if(typeof field.options=='undefined') return false
      return true
    },
    swap: function(x, y) {
      tmp = this.pos[x]
      this.pos[x] = this.pos[y]
      this.pos[y] = tmp
      this.update()
    },
    update: function() {
      this.ivalue = JSON.stringify(this.pos)
    },
    beforeCreate: function(){
      this.pos=JSON.parse(this.value)
      this.ivalue = this.value
    },
    addTxt: function() {
      return g.tr('Add') ?? 'Add'
    }
  }
})

Vue.component('input-media', {
  template: '<div class="pointer:hover shadow:hover;" \
  style="background:var(--main-input-color);max-width:100%;max-height:100%;display: grid;\
  justify-content:center; align-content:center; position:relative;min-width:50px;overflow: hidden;" \
  @click="selectPhoto()" :style="{width:size+\'px\',height:size+\'px\'}">\
<img v-if="!value" src="assets/core/camera.svg" style="width:30px;margin:auto">\
<img v-if="value" :src="imgSrc(value)" style="max-width:100%;margin:auto">\
<svg v-if="value" height="28" width="28" @click.stop="value=null;return false;"\
style="position:absolute;right:0;top:0" viewBox="0 0 28 28">\
  <circle cx="14" cy="14" r="10" stroke-width="0" fill="#666"></circle>\
  <line x1="9" y1="9" x2="18" y2="18" style="stroke:#fff;stroke-width:3"></line>\
  <line x1="9" y1="18" x2="18" y2="9" style="stroke:#fff;stroke-width:3"></line>\
</svg>\
<input v-model="value" type="hidden" :id="\'imd\'+idByName()" :name="name">\
<input type="file" ref="uploader" accept="image/*" multiple style="display:none" @change="uploadPhoto(this)">\
</div>',
  props: ['name','value','fieldset','size'],
  data: function() {
    if(typeof this.size=='undefined') this.size=70
    return {
      field: [],
      value: this.value,
      size: this.size
    }
  },
  methods:{
    imgSrc: function(src) {
      if (src.startsWith('assets/') || src.startsWith('tmp/')) {
        return src
      }
      if(src.startsWith('http:') || src.startsWith('https:') || src.split('.').pop()=='svg') {
        return src;
      }
      return 'lzld/thumb?media_thumb=120&src='+src;
    },
    idByName: function() {
      id = this.name.replace("[", "_");
      return id.replace("]", "_")
    },
    beforeCreate: function() {
      if(typeof this.fieldset!=='undefined') {
        this.field = JSON.parse(this.fieldset)
      }
    },
    selectPhoto: function() {
      open_media_gallery('#imd'+this.idByName())
      //this.$refs['uploader'].click()
    },
    cleanThumb: function() {
      _iUploadMedia=this
      this.value='';
      setTimeout(function () {
        _iUploadMedia.inisrc='';
        _iUploadMedia.value='';
      }, 15);
    },
    uploadPhoto: function() {
      let fm=new FormData()
      _iUploadMedia=this
      uploaded = this.$refs['uploader'].files[0]
      fm.append('uploadfiles', uploaded);
      this.value = true
      var img=this.$refs['thumb']??this.$refs['thumb2']            
      img.file = uploaded;    
      var reader = new FileReader();
      reader.onload = (function(aImg) { 
          return function(e) { 
              aImg.src = e.target.result; 
          };
      })(img);
      reader.readAsDataURL(uploaded);

      g.loader()
      g.ajax({url:"user/uploadImage",method:'POST',data:fm, fn: function (data){
        data = JSON.parse(data)
        g.loader(false)
        console.log(data.image)
       _iUploadMedia.value = data.image
      }})
    }
  }
})


Vue.component('input-gallery', {
  template: '<div style="display: grid; gap: 0.5em; width: 100%;\
  grid-template-columns: repeat(auto-fit,minmax(50px,70px));\
  grid-template-rows: repeat(auto-fit, 70px);">\
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
  <span v-if="values.length==0" style="opacity:0.5">{{placetext}}</span>\
  <span v-for="(value,i) in values" @click="toggle(value)"\
  style="color:white;background:var(--main-primary-color);margin-right:4px;padding:4px;border-radius:4px">\
  &times; {{opList[value]}}</span>&nbsp;\
  <div v-if="dropdown" style="position:absolute; min-width:160px; padding:0;\
  margin:12px -12px;border:1px solid lightgrey; z-index:2;\
  background:white;">\
  <div style="float:right;font-size:150%;margin:0 4px" @click.stop="dropdown=false">&times;</div>\
  <div v-for="(op,i) in opList" style="padding:6px" @click="toggle(i)" v-html="optionDisplay(op,i)"></div>\
  </div>\
  <input v-for="(v,i) in values" type="hidden" :value="v" :name="name+\'[\'+i+\']\'">\
  <input v-if="values.length==0" type="hidden" value="" :name="name">\
</div>',
  props: ['name','value','options','placeholder'],
  data: function() {
    return {
      values: JSON.parse(this.value)??[],
      placetext: this.placeholder??'...',
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

Vue.component('input-keywords', {
  template: '<div :id="\'gm-\'+name+value" style="min-width:180px;cursor:pointer;\
  background:var(--main-input-color);position:relative" @click="dropdown=!dropdown">\
  <span v-if="values.length==0" style="opacity:0.5">{{placetext}}</span>\
  <span v-for="(tag,i) in values" @click="toggle(tag)"\
  style="color:white;background:var(--main-primary-color);display: inline-block;\
  margin:6px;padding:4px;border-radius:4px;word-break: break-all;">\
  &times; {{tag}}</span>&nbsp;\
  <input v-model="newTag" @keypress.stop="keyPressed($event)"\
  style="border-bottom:1px solid rgba(0,0,0,0.5)">\
  <div v-if="dropdown && tagList.length>0" style="position:absolute; min-width:160px; padding:0;\
  margin:12px -12px;border:1px solid lightgrey; z-index:1;\
  background:white;">\
  <div style="float:right;font-size:150%;margin:0 4px" @click.stop="dropdown=false">&times;</div>\
  <div v-for="tag in tagList" style="padding:6px" @click="toggle(tag)" v-html="tagDisplay(tag)"></div>\
  </div>\
  <input v-for="(v,i) in values" type="hidden" :value="v" :name="name+\'[\'+i+\']\'">\
  <input v-if="values.length==0" type="hidden" value="" :name="name">\
</div>',
  props: ['name','value','keywords','placeholder'],
  data: function() {
    return {
      values: JSON.parse(this.value)??[],
      placetext: this.placeholder?this.placeholder:' ...',
      tagList: this.keywords? this.keywords.split(','): [],
      dropdown: false,
      newTag: ''
    }
  },
  methods: {
    toggle: function(i) {
      var index = this.values.indexOf(i);
      if (index === -1) this.values.push(i); else this.values.splice(index, 1);
      this.dropdown=false
    },
    tagDisplay: function(tag) {
      var index = this.values.indexOf(tag);
      if (index !== -1) tag = tag+" &#10003;";
      return tag
    },
    keyPressed: function(event) {
      var i = this.newTag.trim()
      if(['Enter'].includes(event.key)) {
        var index = this.values.indexOf(i);
        if (index === -1) this.values.push(i);
        this.newTag = ''
      } else if(event.key!=',') {
        this.newTag += event.key
      }
      event.preventDefault()
    },
    valuesSplit: function() {
      return this.values.split()
    }
  }
})

Vue.component('color-palette', {
  template: '<div style="">\
  <input type="color" v-for="(color,i) in colors" :value="color" v-model="colors[i]" @change="custom()" :title="labelList[i]">\
  <br><span v-for="(palette,i) in paletteList" @click="changePalette(i)" style="border:1px solid lightgrey;cursor:pointer;padding:2px 6px;" :class="{\'g-selected\':i==selected}">\
  <span v-if="i<paletteList.length-1">{{i+1}}</span><span v-else>★</span></span>\
  <input v-model="value" type="hidden" :id="\'imd\'+idByName()" :name="name">\
</div>',
  props: ['name','value','palettes','labels'],
  data: function() {
    labels = ['','','','','','','','','']
    palettes = null
    if(this.palettes) palettes = JSON.parse(this.palettes)
    if(this.labels) labels = JSON.parse(this.labels)
    return {
      colors: JSON.parse(this.value),
      paletteList: palettes,
      labelList: labels,
      selected: palettes.length-1
    }
  },
  methods:{
    idByName: function() {
      id = this.name.replace("[", "_");
      return id.replace("]", "_")
    },
    changePalette: function(i) {
      this.selected=i
      this.colors = this.paletteList[this.selected].map((x) => x);
    },
    custom: function() {
      this.selected = this.paletteList.length-1
      this.paletteList[this.selected] = this.colors.map((x) => x);
    }
  },
  updated: function() {
    this.value = JSON.stringify(this.colors)
  }
})

Vue.component('tree-select', {
  template: '<div>\
  <select v-model="selectValue" @change="selected()">\
    <option v-if="level>0" value".." key="-1">←</option>\
    <option v-for="(op,i) in options" :value"op.id" :key="i">{{op.label}}</option>\
  </select>\
  <input v-model="value" type="hidden" :name="name">\
</div>',
  props: ['name','value','data'],
  data: function() {
    selected = [null]
    if(this.value) {
      selected = JSON.parse(this.value)
    }
    data = JSON.parse(this.data)

    return {
      level: selected.length,
      treeData: data,
      options: data,
      selected: selected,
      selectValue: null
    }
  },
  methods:{
    selected: function() {
      if(this.selected[this.level-1]=='..') {
        this.level--
        this.selected[this.level-1] = null
      }
      this.updateOpList()
    },
    updateOpList: function () {
      options = this.treeData
      for(i=0; i<level-1; i++) {
        for(child in options) if(child.id==this.selected[i]) {
          options = options[i].children
          break
        }
      }
      return options
    }
  },
  updated: function() {
    this.value = JSON.stringify(this.selected)
  }
})
