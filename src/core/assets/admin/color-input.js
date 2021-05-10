

Vue.component('color-input', {
    template: '<div style="display:flex;gap:0.5em;padding:0.5em 0;margin:0;position:relative">\
<input v-model="ivalue" type="color" ref="input" :name="name"\
style="background:none;width:2em;height:2em;margin:0.2em 0;position: absolute;">\
<input v-model="ivalue" style="padding-left: 2.4em;margin: 0;width: 110px;">\
<div v-for="(color,key) in paletteList" @click="ivalue=color" :style="{background:color}"\
style="border-radius:1em;border:1px solid #66666644;width:2em;height:2em;color:white;padding:0.4em 0.55em">\
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
  }
})
