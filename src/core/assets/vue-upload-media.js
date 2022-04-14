
Vue.component('input-upload-media', {
  template: '<div class="pointer:hover shadow:hover;" \
  style="background:var(--main-input-color);width:120px;height:120px;max-width:100%;max-height:100%;display: grid;\
  justify-content:center; align-content:center; position:relative;min-width:50px;overflow: hidden;"\
  @click="selectPhoto()">\
<img v-if="!inisrc" ref="thumb" src="assets/core/default-user.png" style="width:auto;max-width:100%;margin:auto">\
<img v-if="inisrc" ref="thumb2" :src="imgSrc(inisrc)" style="max-width:100%;margin:auto">\
<svg v-if="value" height="28" width="28" @click.stop="cleanThumb();return false"\
style="position:absolute;right:0;top:0" viewBox="0 0 28 28">\
  <circle cx="14" cy="14" r="10" stroke-width="0" fill="#666"></circle>\
  <line x1="9" y1="9" x2="18" y2="18" style="stroke:#fff;stroke-width:3"></line>\
  <line x1="9" y1="18" x2="18" y2="9" style="stroke:#fff;stroke-width:3"></line>\
</svg>\
<input v-model="value" type="hidden" :id="\'imd\'+idByName()" :name="name">\
<input type="file" ref="uploader" accept="image/*" multiple style="display:none" @change="uploadPhoto(this)">\
</div>\
',
  props: ['name','value','fieldset'],
  data: function() {
    return {
      field: [],
      value: this.value,
      inisrc: this.value
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
    cleanThumb: function() {
      if(this.$refs['thumb']) {
        this.$refs['thumb'].src = 'assets/core/default-user.png'
      }
      _iUploadMedia=this
      this.value='';
      setTimeout(function () {
        _iUploadMedia.inisrc='';
        _iUploadMedia.value='';
      }, 15);
    },
    selectPhoto: function() {
      this.$refs['uploader'].click()
    },
    imgSrc: function(src) {
      if (src.startsWith('assets/') || src.startsWith('tmp/')) {
        return src
      }
      return 'lzld/thumb?media_thumb=120&src='+src
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
       _iUploadMedia.value = data.image
      }})
    }
  }
})
