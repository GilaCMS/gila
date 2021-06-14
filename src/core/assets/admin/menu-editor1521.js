

Vue.component('menu-editor', {
    template: '<dir style="padding:0"><table class="g-table"><tbody>\
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
</select></span>\
<span v-if="row.type==\'link\'"><input v-model="row.title" @input="update"  :placeholder="displayText()"></span>\
<span v-if="row.type==\'link\'"><input v-model="row.url" @input="update" placeholder="Url"></span>\
<span v-else><select v-model="row.id"  @change="update">\
<option v-if="types[row.type]" v-for="(option,i) in types[row.type]" :value="i">{{option}}</option>\
</select></span>\
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
  props: ['name','value','itemtypes'],
  data: function(){ 
    types = new Array()
    if(typeof this.itemtypes!=='undefined') {
      types = JSON.parse(this.itemtypes)
    }
    console.log(this.itemTypes)
    pos = JSON.parse(this.value)
    for(i=0; i<pos.length; i++) {
      if(typeof pos[i].type==='undefined') pos[i].type='link'
      if(typeof pos[i].title==='undefined') pos[i].title=pos[i].name??''
      if(typeof pos[i].id==='undefined') pos[i].id=''
    }
    return {
      pos: pos,
      ivalue: this.value,
      types: types
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
      console.log(tmp)
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
    },
    displayType: function(type) {
      if(type=='link') return 'URL'
      if(type=='page') return g.tr('Page', {'es':'Pagina'})
      if(type=='system') return g.tr('System', {'es':'Sistema'})
    },
    displayText: function() {
      return  g.tr('Title', {'es':'Titulo'})
    }
  }
})
