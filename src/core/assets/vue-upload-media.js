
Vue.component('input-upload-media', {
  template: '<div class="pointer:hover shadow:hover;" \
  style="background:var(--main-input-color);width:160px;height:160px;max-width:100%;max-height:100%;display: grid;\
  justify-content:center; align-content:center; position:relative;min-width:50px;overflow: hidden;" \
  @click="selectPhoto()">\
<img v-if="!value" src="assets/core/camera.svg" style="width:50px;margin:auto">\
<img v-if="value" :src="\'lzld/thumb?media_thumb=160&src=\'+value" style="max-width:100%;margin:auto">\
<svg v-if="value" height="28" width="28" @click.stop="value=null;return false;"\
style="position:absolute;right:0;top:0" viewBox="0 0 28 28">\
  <circle cx="14" cy="14" r="10" stroke-width="0" fill="#666"></circle>\
  <line x1="9" y1="9" x2="18" y2="18" style="stroke:#fff;stroke-width:3"></line>\
  <line x1="9" y1="18" x2="18" y2="9" style="stroke:#fff;stroke-width:3"></line>\
</svg>\
<input v-model="value" type="hidden" :id="\'imd\'+idByName()" :name="name">\
<input type="file" ref="uploader" accept="image/*" multiple style="visibility:hidden" @change="uploadPhoto()">\
</div>\
',
  props: ['name','value','fieldset'],
  data: function() {
    return {
      field: [],
      value: this.value
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
    },
    selectPhoto: function() {
      this.$refs['uploader'].click()
    },
    uploadPhoto: function() {
      let fm=new FormData()
      let _iUploadMedia=this
      fm.append('uploadfiles', this.$refs['uploader'].files[0]);
      g.loader()
      g.ajax({url:"user/uploadImage",method:'POST',data:fm, fn: function (data){
        g.loader(false)
        data = JSON.parse(data)
        _iUploadMedia.value = 'lzld/thumb?media_thumb=160&src='+data.image
      }})
    }
  }
})
