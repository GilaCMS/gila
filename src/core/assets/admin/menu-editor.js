

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
<span><input v-model="pos[key].label" @input="update"></span>\
<span><input v-model="pos[key].url" @input="update" placeholder="url"></span>\
<select v-if="pos[key].type==\'page\'">\
<option v-for="(op,i) in field.options" :value="i">{{op}}</option>\
</select>\
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
  props: ['name','value'],
  data: function(){ 
    return {
      pos: JSON.parse(this.value),
      ivalue: this.value
    }
  },
  methods:{
    add: function(){
      array = new Array()
      array['label'] = ''
      array['url'] = ''
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
    }
  }
})
