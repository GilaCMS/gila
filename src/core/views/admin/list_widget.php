<link rel="stylesheet" type="text/css" href="lib/pnk/pnk.css?">
<div class='pnk-table' pnk-src='src/core/tables/widget' pnk-table="widget" id='twidget'></div>

<?=view::script('lib\jquery\jquery-3.3.1.min.js')?>
<?=view::script('lib\pnk\pnk-1.4.js')?>

<script src="lib/CodeMirror/codemirror.js"></script>
<link rel="stylesheet" href="lib/CodeMirror/codemirror.css">
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
		g('#gila-popup').remove();
	}})
}}

PNK.commands.edit_widget = { fa: "pencil", title: "Edit", fn: function(e){
    href='admin/widgets?id='+e.row_id;
    g.ajax(href,function(data){
        g.dialog({class:'lightscreen',body:data,buttons:'update_widget'})
		textareas=g('.codemirror-js').all
		for(i=0;i<textareas.length;i++) {
			cmirror[i]=CodeMirror.fromTextArea(textareas[i],{lineNumbers:true,mode:'javascript'});
		}
		if(typeof pnk_populate_tables == 'function') pnk_populate_tables(document);
		app = new Vue({
		    el: '#widget_options_form'
		})
    });
}};


Vue.component('input-links', {
    template: '<div>\
<div v-for="(value,key) in pos">\
<input v-for="(field,fkey) in fields" v-model="pos[key][fkey]" @input="update" :placeholder="field.toUpperCase()">\
&nbsp;<button @click="removeEl(key)" class="btn btn-error">-</button>\
</div>\
<a @click="add" class="btn btn-success">+</a>\
<input v-model="value"  type="hidden" :name="name" >\
</div>\
',
    props: ['name','value','fieldset'],
    data: function(){ return {
      pos: JSON.parse(this.value),
	  fields: JSON.parse(this.fieldset),
    }
  },
  methods:{
    add: function(){
		event.preventDefault()
      this.pos.push(['',''])
      this.update()
    },
    removeEl: function(index){
		event.preventDefault()
      this.pos.splice(index,1)
      this.update()
    },
    update: function(){
		this.value=JSON.stringify(this.pos)
    },
    beforeCreate: function(){
		this.pos=JSON.parse(this.value)
		this.fields=JSON.parse(this.fieldset)
    }
  }
})

</script>
