rootVueGTables = []

Vue.component('g-table', {
  template: '<div class="g-table">\
    <div v-if="edititem==0" class="g-table-head">\
      <div>\
      <div class="g-table-title" v-html="table.title"></div>\
        <div v-if="table.group" style="position:relative;display:inline-block" class="search-box">\
          <select v-model="group" class="g-input" @change="runsearch()">\
          <option v-for="g in table.group" :value="g">{{field_label(g)}}</option>\
          </select>\
        </div>\
        <div v-if="table[\'search-box\']" style="position:relative;display:inline-block" class="search-box">\
          <input v-model="search" class=" g-input" @keypress="if($event.keyCode || $event.which == \'13\') runsearch()" value="" type="text">\
          <svg height="24" width="24" style="position:absolute;right:8px;top:8px" viewBox="0 0 28 28"><circle cx="12" cy="12" r="8" stroke="#929292" stroke-width="3" fill="none"></circle><line x1="17" y1="17" x2="24" y2="24" style="stroke:#929292;stroke-width:3"></line></svg>\
        </div>\
        <div v-if="table[\'search-boxes\']" v-for="sb in table[\'search-boxes\']" style="position:relative;display:inline-block" class="search-box">\
          <label>&nbsp;{{field_label(sb)}}</label>\
          <select v-if="table.fields[sb].options" v-model="filter[sb]" class="g-input" @change="runsearch()">\
            <option value="" selected>-</option>\
            <option v-for="(opt,iopt) in table.fields[sb].options" :value="iopt">{{opt}}</option>\
          </select>\
          <div v-else style="position:relative;display:inline-block">\
            <input v-model="search" class="g-input" v-model="filter[sb]" @keypress="if($event.keyCode || $event.which == \'13\') runsearch()" value="" type="text">\
            <svg height="24" width="24" style="position:absolute;right:8px;top:8px" viewBox="0 0 28 28"><circle cx="12" cy="12" r="8" stroke="#929292" stroke-width="3" fill="none"></circle><line x1="17" y1="17" x2="24" y2="24" style="stroke:#929292;stroke-width:3"></line></svg>\
          </div>\
        </div>\
      </div>\
      <span v-if="table.tools" style="float:right">\
        <span v-for="(tool,itool) in table.tools" @click="runtool(tool,$event)" class="g-btn" style="margin-right:6px" v-html="tool_label(tool)"></span>\
      </span>\
    </div>\
    <div v-if="edititem" class="edititem">\
      <span v-if="edititem>0" class="btn" @click="edititem=0"><i class="fa fa-chevron-left" aria-hidden="true"></i></span> \
      <label class="g-label" v-html="table.title"></label>\
      <form :id="name+\'-edit-item-form\'" class="g-form" v-html="edit_html">\
      </form>\
      <div>\
        <a v-if="edititem==\'new\'" class="btn btn-primary" @click="update()" v-html="word(\'Create\')"></a>\
        <a v-else class="btn btn-primary" @click="update()" v-html="word(\'Update\')"></a>\
        <a class="btn btn-white" @click="edititem=false" v-html="word(\'Cancel\')"></a>\
      </div>\
    </div>\
    <g-table v-for="(child,childkey) in table.children" v-if="edititem>0 && child.list" :gtype="childkey" gchild=1 :gtable="JSON.stringify(child.table)" :gfields="JSON.stringify(child.list)" :gfilters="\'&amp;\'+child.parent_id+\'=\'+edititem"></g-table>\
\
    <table v-if="edititem==0 || child==1" class="" cur-page="1"  group-by="">\
    <thead>\
      <tr>\
        <th v-if="table.bulk_actions" style="width:28px;" @click="select_all()">\
          <i class="fa fa-square-o bulk_checkbox" aria-hidden="true"></i>\
        </th>\
        <th v-for="ifield in data.fields" :col="ifield" class="sorting" @click="orderBy(ifield)" v-if="showField(ifield)">\
          <i :class="sortiClass(ifield)" :col="ifield""></i>\
          <span v-html="field_label(ifield)"></span>\
        </th>\
        <th v-if="table.commands">\
        </th>\
      </tr>\
    </thead>\
    <tbody>\
      <tr v-for="(row,irow) in data.rows" :row-id="row.id">\
        <td v-if="table.bulk_actions" @click="select_row(row[0])">\
          <i :class="checkboxClass(row[0])"></i>\
        </td>\
        <td v-for="(field,ifield) in data.fields" :col="ifield" :value="row[ifield]" :class="field" v-if="showField(field)">\
          <span v-html="display_cell(irow,ifield)" @click="clicked_cell(irow,ifield)"></span>\
        </td>\
        <td v-if="table.commands" class="td-com">\
          <span v-for="(com,icom) in table.commands" @click="command(com,row[0])" class="g-btn btn-white com-btn" v-html="command_label(com)"></span>\
        </td>\
      </tr>\
      <tr v-if="data.rows.length==0">\
        <td colspan="100">No results found</td>\
      </tr>\
    </tbody>\
    <tfoot v-if="table.pagination">\
      <tr>\
        <td colspan="100">\
          <ul class="pagination g-pagination">\
            <li v-for="p in pagination()" :class="(p==page?\'active\':\'\')" @click="load_page({page:p})" v-html="p"></li>\
          </ul>\
        </td>\
      </tr>\
    </tfoot>\
  </table>\
  </div>\
  ',
  props: ['gtype','gtable','gfields','gfilters','gchild'],
  data: function(){ return {
    name: this.gtype,
    table: JSON.parse(this.gtable),
    data:{
      fields: JSON.parse(this.gfields),
      rows:[],
      totalRows:0
    },
    filters: this.gfilters,
    filter: [],
    selected_rows: [],
    order: "",
    group: null,
    edititem:0,
    edit_html:"",
    search:"",
    page:1,
    type: this.gtype,
    child: this.gchild,
  }},
  updated: function() {
    let textareas
    if(this.edititem==0) return;
    
    textareas=g('.codemirror-js').all
    cmirror=[]
    for(i=0;i<textareas.length;i++) {
      x=textareas[i].name
      cmirror[x]=CodeMirror.fromTextArea(textareas[i],{lineNumbers:true,mode:'javascript'});
    }
    
    textareas=g('.tinymce').all
    mce_editor=[]
    tinymce.remove() //remove all tinymce editors
    for(i=0;i<textareas.length;i++) {
      mce_editor[i] = textareas[i].name;
      g_tinymce_options.selector = '[name='+textareas[i].name+']'
      tinymce.init(g_tinymce_options)
    }
    if(typeof $.fn.select2 != 'undefined') $(".select2").select2();
  },
  methods: {
    load_page: function(a={}) {
      let _data = this.data
      if(a.page) this.page=a.page
      if(a.order) this.order=a.order
      if(a.group) this.group=a.group
      if(typeof this.filters=='undefined') this.filters=''
      order = this.order ? '&orderby='+this.order: '';
      search = this.search ? '&search='+this.search: '';
      group = this.group ? '&groupby='+this.group: '';
      for(fkey in this.filter) {
        if(this.filter[fkey]!=='') search += '&'+fkey+'='+this.filter[fkey]
      }
      g.get('cm/list_rows/'+this.name+'?page='+this.page+this.filters+order+group+search,function(data){
        data = JSON.parse(data)
        _data.rows=data.rows
        _data.totalRows=data.totalRows
      })
    },
    select_row: function(irow) {
      var index = this.selected_rows.indexOf(irow)
      if(index === -1) {
        this.selected_rows.push(irow);
      } else {
        this.selected_rows.splice(index, 1);
      }
    },
    command: function(com, irow) {
      gtableCommand[com].fn(this,irow)
    },
    runtool: function(tool,e) {
      gtableTool[tool].fn(this)
      e.preventDefault()
    },
    tool_label: function(tool) {
      if(typeof gtableTool[tool]=='undefined') return _e(tool)
      return _e(gtableTool[tool].label)
    },
    field_label: function(ifield) {
      if(typeof this.table.fields[ifield].title=='undefined') return ifield
      return this.table.fields[ifield].title
    },
    command_label: function(com) {
      if(typeof gtableCommand[com]=='undefined') return com
      return '<i class="fa fa-2x fa-'+gtableCommand[com].fa+'"></i>'
    },
    runsearch: function() {
      this.load_page()
    },
    update: function(){
      let irow = this.edititem
      id_name = this.name+'-edit-item-form'
      
      form = document.getElementById(id_name)
      data = new FormData(form);

      for (x in mce_editor)  {
        console.log(mce_editor[x])
        data.set(mce_editor[x], tinymce.get(mce_editor[x]).getContent())
        console.log(tinymce.get(mce_editor[x]).getContent()) 
      }
      textareas=g('.codemirror-js').all
      for (x in cmirror) {
        data.set(x, cmirror[x].getValue())
        console.log( cmirror[x].getValue())
      }

      let _this = this
      if(irow=='new') {
        url = 'cm/update_rows/'+this.name
      } else {
        url = 'cm/update_rows/'+this.name+'?id='+irow
      }
      g.ajax({method:'post',url:url,data:data,fn:function(data) {
        data = JSON.parse(data)
        if(irow=='new') {
          _this.data.rows.unshift(data.rows[0])
        } else {
          _this.update_row(data.rows[0])
        }
        this.$forceUpdate()
      }})
      this.edititem = false
    },
    toggle_value: function(irow,ifield,v1=0,v2=1) {
      //if(this.data.rows[irow][ifield]==0) this.data.rows[irow][ifield]=1; else this.data.rows[irow][ifield]=0;
      //this.$forceUpdate()
    },
    update_row: function(row) {
      for(i=0; i<this.data.rows.length; i++) if(this.data.rows[i][0] == row[0]){
        this.data.rows[i] = row;
        this.$forceUpdate()
      }
    },
    clicked_cell: function(irow,ifield){
      fkey = this.data.fields[ifield]
      field = this.table.fields[fkey]
      if (typeof field.type != "undefined") if(field.type=='checkbox') {
        this.toggle_value(irow,ifield,0,1)
      }
      // update with api
    },
    display_cell: function(irow,ifield){
      fkey = this.data.fields[ifield]
      rv = this.data.rows[irow]
      cv = this.data.rows[irow][ifield]
      field = this.table.fields[fkey]
      dv = cv // display value
      if (typeof this.table.fields[fkey].display != "undefined") {
        return eval(this.table.fields[fkey].display)
      }
      if (typeof this.table.fields[fkey].eval != "undefined") {
        return eval(this.table.fields[fkey].eval)
      }

      if(typeof gtableFieldDisplay[fkey]!='undefined') {
        for(let i=0;i<this.data.fields.length ;i++) {
          f = this.data.fields[i]
          rv[f] = rv[i]
        }
        return gtableFieldDisplay[fkey](rv);
      }

      if (typeof field.type != "undefined") if(field.type=='checkbox') if(cv==1){
        return '<i style="color:green" class="fa fa-check fa-2x"></i>'//-square-o
      } else {
        return '<i style="color:red" class="fa fa-remove fa-2x"></i>'
      }

      if (typeof field.options != "undefined") if(cv!==null) {
        if (typeof field.options[cv] != "undefined") return field.options[cv]
        let resp = ''
        let csv = cv.split(',')
        for(i=0;i<csv.length;i++)  if(typeof field.options[csv[i]] != "undefined") {
          resp += field.options[csv[i]]+'<br>'
        } else resp += csv[i]+'<br>'
        return resp
      }

      return dv
    },
    showField: function(field) {
      if(typeof this.table.fields[field].show=='undefined') return true
      return this.table.fields[field].show
    },
    word: function(word){
      return _e(word)
    },
    totalPages: function(){
      return Math.ceil(this.data.totalRows/this.table.pagination)+1
    },
    pagination: function(){
      let a = []
      let total =this.totalPages()
      for(i=1;i<4;i++) if(i<total) if(a.indexOf(i)==-1) a.push(i);
      for(i=this.page-3;i<this.page+3;i++) if(i>0 && i<total) if(a.indexOf(i)==-1) a.push(i);
      for(i=total-3;i<total;i++) if(i>0) if(a.indexOf(i)==-1) a.push(i);
      return a
    },
    orderBy: function(key){
      if(this.order==key+'_d') this.order=key+'_a'; else this.order=key+'_d';
      this.load_page({page:1})
    },
    sortiClass: function(key){
      cl=''
      if(this.order==key+'_a') cl='fa-chevron-up'
      if(this.order==key+'_d') cl='fa-chevron-down'
      return 'g-sorti fa '+cl;
    },
    checkboxClass: function(irow){
      cl = ''
      if(this.selected_rows.indexOf(irow)>-1) cl='fa-check-square-o'; else cl='fa-square-o';
      return 'fa tr_checkbox '+cl;
    }
  },
  mounted: function() {
    this.load_page({page:1})
    rootVueGTables.push(this)
  }
})

gtableCommand = Array()
gtableTool = Array()
gtableFieldDisplay = Array()

gtableCommand['edit'] = {
  fa: "pencil",
  fn: function(table,irow){
    let _this = table
    _this.edititem = irow
    _this.edit_html = "Loading..."
    g.loader()
    g.get('cm/edit_form/'+_this.name+'?id='+irow,function(data){
      _this.edit_html = data
      g.loader(false)
    })
  }
}

gtableCommand['edit_popup'] = {
  fa: "pencil",
  fn: function(table,irow) {
  href='cm/edit_form/'+table.name+'?id='+irow;
    g.ajax(href,function(data){
      g.dialog({class:'lightscreen large',body:data,type:'modal',buttons:'popup_update'})
      app = new Vue({
        el: '#'+table.name+'-edit-item-form'
      })
      textareas=g('.codemirror-js').all
      for(i=0;i<textareas.length;i++) {
        cmirror[i]=CodeMirror.fromTextArea(textareas[i],{lineNumbers:true,mode:'javascript'});
      }
    })
  }
}

g.dialog.buttons.popup_update = {title:'Update', fn:function(e){
  form = g('#gila-popup form').all[0]
  data = new FormData(form);
  t = form.getAttribute('data-table')
  id = form.getAttribute('data-id')
  for(i=0; i<rootVueGTables.length; i++) if(rootVueGTables[i].name == t) {
    _this = rootVueGTables[i]
  }

  if(id=='new') {
    url = 'cm/update_rows/'+t
  } else {
    url = 'cm/update_rows/'+t+'?id='+id
  }
  g.ajax({method:'post',url:url,data:data,fn:function(data) {
    data = JSON.parse(data)
    if(id=='new') {
      _this.data.rows.unshift(data.rows[0])
    } else {
      _this.update_row(data.rows[0])
    }
    _this.$forceUpdate()
  }})

  g('#gila-popup').parent().remove();
}};


gtableCommand['clone'] = {
  fa: "copy",
  fn: function(table,id) {
    let _this
    _this = table
    _this.edit_html = "Loading..."
    g.get('cm/insert_row/'+_this.name+'?id='+id,function(data){
      data = JSON.parse(data)
      _this.data.rows.unshift(data.rows[0])
    })
  }
}

gtableCommand['delete'] = {
  fa: "trash-o",
  fn: function(table,id) {
    let _this = table
    let _id = id
    data = new FormData()
    data.append('id',id)
    if(confirm(_e("Delete registry?"))) g.ajax({
      url: "cm/delete?t="+_this.name,
      data: data,
      method: 'post',
      fn: function(data) {
        for(i=0;i<_this.data.rows.length;i++) if(_this.data.rows[i][0] == _id) {
          _this.data.rows.splice(i,1)
          //_this.$forceUpdate()
        }
      }
    });
  }
}

gtableTool['add'] = {
  fa: "plus", label: _e("New"),
  fn: function(table) {
    let _this = table
    _this.edititem = 'new'
    _this.edit_html = "Loading..."
    g.get('cm/edit_form/'+_this.name,function(data){
      _this.edit_html = data
    })
  }
}
gtableTool['addrow'] = {
  fa: "plus", label: _e("New"),
  fn: function(table) {
    let _this
    _this = table
    _this.edit_html = _e("Loading")+"..."
    g.get('cm/insert_row/'+_this.name+'?'+_this.filters,function(data){
      data = JSON.parse(data)
      if(typeof _this.data.rows=='undefined') {
        _this.data.rows = [data.rows[0]]
      } else _this.data.rows.unshift(data.rows[0])
    })
  }
}
gtableTool['csv'] = {
  fa: "arrow-down", label: "Csv",
  fn: function(table) {
    window.location.href = 'cm/csv/'+table.name+'?'+table.filters;
  }
}
gtableTool['log_selected'] = {
  fa: "arrow-down", label: "Csv",
  fn: function(table) {
    console.log(table.selected_rows);
  }
}
gtableTool['uploadcsv'] = {
  fa: "arrow-up", label: "Csv",
  fn: function(table) {
    g.dialog({title:_e("Select"),body:document.getElementById('uploadcsv_msg'),buttons:'select_row_source',type:'modal',class:'large',id:'select_row_dialog'})
  }
}
gtableTool['addfrom'] = {
  fa: "plus", label: _e("New"),
  fn: function(table) {
    let _table
    _table = table.table
    g.post('cm/select_row/'+_table.tool.addfrom[0],"list="+_table.tool.addfrom[1],function(gal){
      g.dialog({title:_e("Select"),body:gal,buttons:'select_row_source',type:'modal',class:'large',id:'select_row_dialog'})
      app.table_to_insert = _table.name
    })
  }
}

g_tinymce_options = {
  selector: '',
  relative_urls: false,
  remove_script_host: false,
  height: 250,
  theme: 'modern',
  extended_valid_elements: 'script,div[v-for|v-if|v-model|style|class|id|data-load]',
  plugins: [
    'lists link image hr anchor pagebreak',
    'searchreplace wordcount visualchars code',
    'insertdatetime media nonbreaking table contextmenu ',
    'template paste textcolor textpattern codesample'
  ],
  toolbar1: 'styleselect | forecolor backcolor bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link media image codesample',
  file_picker_callback: function(cb, value, meta) {
    input_filename = cb;
    open_gallery_post();
  },
}

// Translation
function _e(phrase)
{
  if(typeof lang_array!='undefined') if(typeof lang_array[phrase]!='undefined') return lang_array[phrase];
  return phrase;
}



g.dialog.buttons.select_path = {
  title:'Select',fn: function(){
    let v = g('#selected-path').attr('value')
    if(v!=null) g('[name=p_img]').attr('value', base_url+v)
    g('#media_dialog').parent().remove();
  }
}
g.dialog.buttons.select_path_post = {
  title:'Select', fn: function() {
    let v = g('#selected-path').attr('value')
    if(v!=null) input_filename(base_url+v);
    g('#media_dialog').parent().remove();
  }
}
g.dialog.buttons.select_row_source = {
  title:'Select', fn: function() {
    let v = g('#selected-row').attr('value')
    alert(v);
    g('#select_row_dialog').parent().remove();
  }
}
function open_gallery() {
  g.post("admin/media","g_response=content",function(gal){
    g.dialog({title:"Media gallery",body:gal,buttons:'select_path',type:'modal',class:'large',id:'media_dialog'})
  })
}
function open_gallery_post() {
  g.post("admin/media","g_response=content",function(gal){ 
    g.dialog({title:"Media gallery",body:gal,buttons:'select_path_post',type:'modal',class:'large',id:'media_dialog'})
  })
}
function open_select_from_table(t) {
  g.post("admin/content/"+t,"g_response=content",function(gal){ 
    g.dialog({title:"Media gallery",body:gal,buttons:'select_path_post',type:'modal',class:'large',id:'media_dialog'})
  })
}
function open_select_row(row,table) {
  input_select_row = row;
  g.post("cm/select_row/"+table,"",function(gal){
    g.dialog({title:__m('_gallery'),body:gal,buttons:'select_row_source',type:'modal',id:'select_row_dialog',class:'large'})
  })
}

g.click(".select-row",function(){
  g('.select-row').removeClass('g-selected');
  g(this).addClass('g-selected');
  g('#selected-row').attr('value',this.getAttribute('data-id'))
})
