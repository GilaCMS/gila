

Vue.component('menu-editor', {
    template: '<dir style="padding:0"><table class="g-table" style="width:100%"><tbody>\
<tr v-for="(row,key) in pos">\
<td>\
<span v-if="key>0" style="cursor:pointer;padding:0.5em 0.5em;color:black" @click="swap(key,key-1)">&uarr;</span>\
<span v-else style="padding:0.5em 0.5em;opacity:0">&uarr;</span>\
<span v-if="key<pos.length-1" style="cursor:pointer;padding:0.5em 0.5em;color:black" @click="swap(key,key+1)">&darr;</span>\
<span v-else style="padding:0.5em 0.5em;opacity:0">&darr;</span>\
</td>\
<td>\
  <span><select v-model="row.type" @change="update">\
  <option value="link">{{displayType(\'link\')}}</option>\
  <option v-for="(type,i) in types" :value="i">{{displayType(i)}}</option>\
  <option v-if="name" value="dir">{{displayType(\'dir\')}}</option>\
  <option v-if="name" value="btn">{{displayType(\'btn\')}}</option>\
  <option v-if="name" value="btn2">{{displayType(\'btn2\')}}</option>\
  </select></span>\
  <span v-if="hasTitle(row.type)"><input v-model="row.title" @input="update"  :placeholder="displayText()"></span>\
  <span v-if="hasURL(row.type)"><input v-model="row.url" @input="update" placeholder="Url"></span>\
  <span v-if="typeof types[row.type]!=\'undefined\'">\
    <select v-model="row.id"  @change="update">\
    <option v-if="types[row.type]" v-for="(option,i) in types[row.type]" :value="i">{{option}}</option>\
    </select></span>\
  <div v-if="row.type==\'dir\'">\
  <menu-editor  @event="updateFolder" :i="key" :itemtypes="itemtypes" :value=\'JSON.stringify(row.children)\'></div>\
</td>\
<td>\
  <span @click="removeEl(key)" style="cursor:pointer;padding:0.4em;color:#444;font-size:150%">&times;</span>\
</td>\
</tr>\
</tbody></table>\
<span @click="add()" class="btn"\
style="padding:0.3em;font-family: inherit;color: inherit;background: inherit;">+ {{addTxt()}}</span>\
<input v-if="name" v-model="ivalue" type="hidden" :name="name" >\
</div>\
',
  props: ['name','value','itemtypes','i'],
  data: function(){ 
    types = new Array()
    if(typeof this.itemtypes!=='undefined') {
      types = JSON.parse(this.itemtypes)
    }
    console.log(this.itemtypes)
    try {
      pos = JSON.parse(this.value)
    } catch (e) {
      pos=[];
    }
    for(i=0; i<pos.length; i++) {
      if(typeof pos[i].type==='undefined') pos[i].type='link'
      if(typeof pos[i].title==='undefined') pos[i].title=pos[i].name??''
      if(typeof pos[i].id==='undefined') pos[i].id=''
    }
    return {
      pos: pos,
      ivalue: this.value,
      types: types,
      index: this.i
    }
  },
  methods:{
    add: function(){
      array = {type:'link',title:'',url:''}
      this.pos.push(array)
      this.update()
    },
    removeEl: function(index){
      this.pos.splice(index,1)
      this.update()
    },
    swap: function(x, y) {
      tmp = this.pos[x]
      this.pos[x] = this.pos[y]
      this.pos[y] = tmp
      this.update()
      this.$forceUpdate()
    },
    updateFolder: function(param) {
      this.pos[param[0]].children=param[1]
      this.ivalue = JSON.stringify(this.pos)
    },
    update: function() {
      if(typeof this.name=='undefined') {
        this.$emit('event', [this.index, this.pos])
        return
      }
      this.ivalue = JSON.stringify(this.pos)
    },
    beforeCreate: function(){
      this.pos=JSON.parse(this.value)
      this.ivalue = this.value
    },
    addTxt: function() {
      return g.tr('Add') ?? 'Add'
    },
    displayType: function(type) {
      if(type=='link') return 'URL'
      if(type=='page') return g.tr('Page', {'es':'Pagina'})
      if(type=='system') return g.tr('System', {'es':'Sistema'})
      if(type=='dir') return g.tr('Folder', {'es':'Carpeta'})
      if(type=='btn') return g.tr('Button A', {'es':'Botón A'})
      if(type=='btn2') return g.tr('Button B', {'es':'Botón B'})
    },
    displayText: function() {
      return  g.tr('Title', {'es':'Titulo'})
    },
    hasTitle: function(type) {
      if(type=='link'||type=='btn'||type=='btn2'||type=='dir') return true
      return false
    },
    hasURL: function(type) {
      if(type=='link'||type=='btn'||type=='btn2') return true
      return false
    }
  }
})
