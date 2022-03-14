Vue.component('color-input', {
  template: '<div style="display:flex;gap:0.5em;padding:0.5em 0;margin:0;position:relative">\
<div :style="{background:previewBG(ivalue)}" ref="preview" @click="$refs.input.click()"\
style="border-radius:1em;border:1px solid #44444466;width:2em;height:2em;color:white;padding:0.4em 0.55em"></div>\
<input v-model="ivalue" type="hidden" :name="name">\
<input v-model="ivalue" type="color" ref="input"\
style="margin:0;padding:0;visibility: hidden;width:0px">\
<div v-for="(color,key) in paletteList" @click="selectPC(key)"\
:style="{background:previewBG(color)}"\
style="border-radius:0.8em;border:1px solid #44444466;width:1.6em;height:1.6em;color:white;margin-top:0.2em;padding:0.15em 0.35em">\
<span v-if="ivalue==color"><i class="fa fa-check-circle" style="text-shadow:0 0 3px #000"></i></span>\
</div>\
</div>\
',
props: ['name','value','palette'],
data: function(){
  return {
    ivalue: this.value,
    paletteList: JSON.parse(this.palette)
  }
},
methods: {
  selectPC: function(i){
    this.ivalue=this.paletteList[i]
  },
  previewBG: function(c){
    if(c.startsWith('var(--p')||c.startsWith('var(--rgb')) return 'rgb('+c+')'
    return c
  }
}
})
