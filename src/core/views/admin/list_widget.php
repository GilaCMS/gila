<link rel="stylesheet" type="text/css" href="lib/pnk/pnk.css?">
<div class='pnk-table' pnk-src='src/core/tables/widget' pnk-table="widget" id='twidget'></div>

<?=view::script('lib\jquery\jquery-3.3.1.min.js')?>
<?=view::script('lib\pnk\pnk-1.4.js')?>

<script src="lib/CodeMirror/codemirror.js"></script>
<link rel="stylesheet" href="lib/CodeMirror/codemirror.css">
<style>.CodeMirror{max-height:150px;border:1px solid var(--main-border-color);width:100%}</style>
<script src="lib/CodeMirror/javascript.js"></script>
<?=view::script('src/core/assets/admin/media.js')?>
<?=view::script('lib/vue/vue.min.js');?>
<style>.circle{border-radius: 50%}</style>

<script>

pnk_populate_tables(document);
requiredRes = [];
cmirror=new Array()

g.dialog.buttons.update_widget = {title:'Update',fn:function(){
	textareas=g('.codemirror-js').all
	for(i=0;i<textareas.length;i++) {
		textareas[i].value=cmirror[i].getValue()
	}
	let fm=new FormData(g.el('widget_options_form'))
    g.ajax({url:'admin/update_widget?g_response=content',method:'POST',data:fm,fn:function(x){
		g('#gila-popup').parent().remove();
	}})
}}

PNK.commands.edit_widget = { fa: "pencil", title: "Edit", fn: function(e){
    href='admin/widgets?id='+e.row_id;
    g.ajax(href,function(data){
        g.dialog({class:'lightscreen large',body:data,type:'modal',buttons:'update_widget'})
		app = new Vue({
		    el: '#widget_options_form'
		})
		textareas=g('.codemirror-js').all
		for(i=0;i<textareas.length;i++) {
			cmirror[i]=CodeMirror.fromTextArea(textareas[i],{lineNumbers:true,mode:'javascript'});
		}
		if(typeof pnk_populate_tables == 'function') pnk_populate_tables(document);
    });
}};

//<span v-if="field==&quot;image&quot;" style="width:28px"  :onclick="\'open_media_gallery(\\\'#il\'+field+key+\'\\\')\'"><i class="fa fa-image"></i></span>\
//<span v-if="field==&quot;image&quot;" style="width:28px"  :onclick="\'open_media_gallery(\\\'#il\'+field+key+\'\\\')\'"><i class="fa fa-image"></i></span>\

Vue.component('input-links', {
    template: '<div>\
<div v-for="(row,key) in pos">\
<span v-for="(field,fkey) in fields">\
	<span v-if="field==&quot;image&quot;" style="width:80px" >\
		<img :src="pos[key][fkey]"  :onclick="\'open_media_gallery(\\\'#il\'+field+key+\'\\\')\'" style="width:80px;max-height:50px;vertical-align:middle" />\
		<input v-model="pos[key][fkey]" type="hidden" :id="\'il\'+field+key" @input="update">\
	</span>\
	<input v-if="field!=&quot;image&quot;" v-model="pos[key][fkey]" :id="\'il\'+field+fkey" @input="update" :placeholder="field.toUpperCase()">\
</span>\
&nbsp;<button @click="removeEl(key)" class="btn btn-error">-</button>\
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
	update: function(){
		this.ivalue = JSON.stringify(this.pos)
    },
    beforeCreate: function(){
		console.log('dfewef')
		this.pos=JSON.parse(this.value)
		this.fields=JSON.parse(this.fieldset)
		this.ivalue = this.value
    }
  }
})

</script>
