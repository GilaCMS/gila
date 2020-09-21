rootVueGTables = []
edit_popup_app = null

Vue.component('g-table', {
  template: '<div class="g-table">\
    <div v-if="edititem" class="edititem">\
      <span v-if="edititem>0 || edititem==\'new\'" class="btn" @click="edititem=0"><i class="fa fa-chevron-left" aria-hidden="true"></i></span> \
      <label class="g-label" v-html="table.title"></label>\
      <form :id="name+\'-edit-item-form\'" class="g-form" v-html="edit_html">\
      </form>\
      <div>\
        <a v-if="edititem==\'new\'" class="btn btn-primary" @click="update()" v-html="word(\'Create\')"></a>\
        <a v-else class="btn btn-primary" @click="update()" v-html="word(\'Update\')"></a>\
        <a class="btn btn-white" @click="edititem=false" v-html="word(\'Cancel\')"></a>\
      </div>\
    </div>\
    <g-table v-for="(child,childkey) in table.children"\
     v-if="edititem>0 && edititem!=\'new\' && child.list" :gtype="childkey"\
     gchild=1 :gtable="JSON.stringify(child.table)" :gfields="JSON.stringify(child.list)"\
     :gfilters="\'&amp;\'+child.parent_id+\'=\'+edititem">\
    </g-table>\
\
    <table v-if="edititem==0 || child==1" class="" cur-page="1"  group-by="" style="position:relative">\
    <thead>\
      <tr><td colspan=100>\
        <div v-if="edititem==0" class="g-table-head">\
        <div><div class="g-table-title" v-html="table.title"></div>\
          <div v-if="table[\'search-box\'] || table[\'search_box\']" class="g-searchbox">\
            <input v-model="search" class=" g-input" @keydown="if($event.which == \'9\') runsearch()"\
            @keyup="if($event.which == \'8\') runsearch()" :autofocus="table[\'search_box_autofocus\']"\
            @keypress="if($event.keyCode || $event.which == \'13\') runsearch()" value="" type="text" style="padding-left:28px">\
            <svg height="24" width="24" style="position:absolute;left:0.3em;top:0.6em" viewBox="0 0 28 28"><circle cx="12" cy="12" r="8" stroke="#929292" stroke-width="3" fill="none"></circle><line x1="17" y1="17" x2="24" y2="24" style="stroke:#929292;stroke-width:3"></line></svg>\
          </div>\
          <div v-if="table.group" style="position:relative;display:inline-block" class="g-searchbox">\
            <select v-model="group" class="g-input" @change="runsearch()">\
            <option v-for="g in table.group" :value="g">{{field_label(g)}}</option>\
            </select>\
          </div>\
          <div v-if="table[\'search_boxes\']" v-for="sb in table[\'search_boxes\']" class="g-searchbox">\
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
        <div>\
          <span v-if="table.bulk_actions && selected_rows.length>0">\
            <span v-for="iaction in table.bulk_actions" @click="runtool(iaction,$event)" class="g-btn btn-white" style="margin-right:6px; font-weight:bold" v-html="tool_label(iaction)"></span>\
          </span>\
          <span v-if="table.tools">\
            <span v-for="(tool,itool) in table.tools" @click="runtool(tool,$event)" class="g-btn" style="margin-right:6px; font-weight:bold" v-html="tool_label(tool)"></span>\
          </span>\
        </div></div></td>\
      </tr>\
      <tr>\
        <th v-if="table.bulk_actions" style="width:28px;" @click="toggleSelectAll()">\
          <i :class="checkboxClassBulk()" aria-hidden="true"></i>\
        </th>\
        <th v-for="ifield in data.fields" :col="ifield" class="sorting" @click="orderBy(ifield)"\
          v-if="showField(ifield)" :style="thStyle(ifield)">\
          <i :class="sortiClass(ifield)" :col="ifield""></i>\
          <span v-html="field_label(ifield)"></span>\
        </th>\
        <th v-if="table.commands">\
        </th>\
      </tr>\
    </thead>\
    <tbody>\
      <tr v-for="(row,irow) in data.rows" :row-id="irow" :class="{selected:selectedRow(row[0])}">\
        <td v-if="table.bulk_actions" @click="select_row(row[0], irow, $event)">\
          <i :class="checkboxClass(row[0])"></i>\
        </td>\
        <td v-for="(field,ifield) in data.fields" v-if="showField(field)"\
        :col="ifield" :value="row[ifield]" :class="field" v-html="display_cell(irow,ifield)"\
        @keydown="inlineDataUpdate(irow, field)">\
        </td>\
        <td v-if="table.commands" class="td-com">\
          <span v-for="(com,icom) in table.commands" @click="command(com,row[0])" class="g-icon-btn com-btn" v-html="command_label(com)"></span>\
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
  props: ['gtype','gtable','gfields','gfilters','gchild','grows','gtotalrows'],
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
    query: '',
    selected_rows: [],
    order: [],
    group: null,
    edititem:0,
    edit_html:"",
    search:"",
    page:1,
    type: this.gtype,
    child: this.gchild,
    bulk_selected: 0,
    updateRows: [],
    inlineEdit: false,
    intervalUpdate: null,
    irowSelected: null
  }},
  updated: function() {
    if(this.edititem==0) return;
    transformClassComponents()
  },
  methods: {
    load_page: function(a={}) {
      let _data = this.data
      if(a.page) this.page=a.page
      if(a.order) this.order=a.order
      if(a.group) this.group=a.group
      if(typeof this.filters=='undefined') this.filters=''
      order = ''
      for (x in this.order) {
        order = order+'&orderby['+x+']='+this.order[x]
      }
      search = this.search ? '&search='+this.search: '';
      group = this.group ? '&groupby='+this.group: '';
      for(fkey in this.filter) {
        if(this.filter[fkey]!=='') search += '&'+fkey+'='+this.filter[fkey]
      }
      g.get('cm/list_rows/'+this.name+'?page='+this.page+this.filters+order+group+search,function(data){
        data = JSON.parse(data)
        _data.rows=data.rows
        _data.totalRows=data.totalRows
        if(typeof lazyImgLoad!='undefined') {
          setTimeout(function(){lazyImgLoad();}, 150);
        }
      })
    },
    select_row: function(rowId, irow=null, event=null) {
      var index = this.selected_rows.indexOf(rowId)
      if(index === -1) {
        this.selected_rows.push(rowId);
      } else {
        this.selected_rows.splice(index, 1);
      }

      if (event && event.shiftKey && this.irowSelected) {
        step = Math.sign(this.irowSelected - irow)
        for(i=irow+step; i!=this.irowSelected+step; i+=step) {
          row_id = this.data.rows[i][0]
          console.log(row_id)
          index2 = this.selected_rows.indexOf(row_id)
          if(index === -1) {
            if(index2===-1) {
              this.selected_rows.push(row_id);
            }
          } else {
            if(index2>-1) {
              this.selected_rows.splice(index2, 1);
            }
          }
        }
      }

      this.bulk_selected = -1;
      if(this.selected_rows.length==0) {
        this.bulk_selected = 0;
      }
      this.irowSelected = irow
    },
    toggleSelectAll: function() {
      this.selected_rows = [];
      if(this.bulk_selected == 0) {
        this.bulk_selected = 1;
        for(i in this.data.rows) {
          this.selected_rows.push(this.data.rows[i][0]);
        }
      } else {
        this.bulk_selected = 0;
      }
    },
    command: function(com, irow) {
      gtableCommand[com].fn(this,irow)
    },
    runtool: function(tool,e) {
      if(tool==0) return;
      this.query=this.filters;
      for(fkey in this.filter) {
        if(this.filter[fkey]!=='') this.query += '&'+fkey+'='+this.filter[fkey]
      }
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
      if(this.table.action_display=='label' ||
        typeof gtableCommand[com]=='undefined') {
          return '<a>'+gtableCommand[com].label+'</a>';
        }
        
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
      values = readFromClassComponents()
      for(x in values) {
        data.set(x, values[x])
      }

      let _this = this
      if(irow=='new') {
        url = 'cm/update_rows/'+this.name
        if(typeof _this.filters!='undefined') {
          url = url+'?'+_this.filters
        }
      } else {
        url = 'cm/update_rows/'+this.name+'?id='+irow
      }
      g.ajax({method:'post',url:url,data:data,fn:function(data) {
        data = JSON.parse(data)
        if(irow=='new') {
          _this.data.rows.unshift(data.rows[0])
          if(typeof _this.table.children!='undefined') {
            _this.edititem = data.rows[0][0]
          }
        } else {
          _this.update_row(data.rows[0])
        }
      }})

      if(irow=='new' && typeof this.table.children!='undefined') {
        return
      }
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
      displayValue = cv

      if(field.alt) if(!cv) {
        cv = field.alt
        displayValue = '<span style="opacity:0.66">'+cv+'</span>'
      }
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

      // Display type
      if (typeof field.display_type != "undefined") {
        displayType = field.display_type;
      } else if (typeof field.input_type != "undefined") {
        displayType = field.input_type;
      } else if (typeof field['input-type'] != "undefined") {
        displayType = field['input-type'];
      } else {
        displayType = field.type;
      }

      if(displayType=='checkbox') if(cv==1){
        return '<i style="color:green" class="fa fa-check fa-2x"></i>'
      } else {
        return '<i style="color:red" class="fa fa-remove fa-2x"></i>'
      }

      if(displayType=='media') if(cv!=null && cv.length>0) {
        return '<img src="lzld/thumb?src='+cv+'&media_thumb=80" style="max-width:80px"></img>'
      } else {
        return '';
      }

      if(displayType=='number') {
        return '<div style="text-align:right">'+displayValue+'</div>';
      }

      if(displayType=='radial-bar') {
        displayValue=parseFloat(displayValue).toFixed(2)
        if (isNaN(displayValue)) return
        pcValue = parseInt(displayValue*100)
        if(field.display_percentage) displayValue=pcValue+'%'
        return '<div style="text-align:center;width:100%"><svg viewBox="0 0 40 40" style="width:28px;vertical-align: middle;">\
        <circle stroke="lightgrey" stroke-width="8" fill="transparent" r="15" cx="20" cy="20"/>\
        <path d="M21 4 a 15 15 0 0 1 0 30 a 15 15 0 0 1 0 -30"\
          fill="none"\ stroke="var(--main-a-color)";\ stroke-width="8";\
          stroke-dasharray="'+pcValue+', 100" />\
      </svg> <span style="vertical-align: middle;">'+displayValue+'</span></div>'
      }

      if(displayType=='text') if (rv.text.length>100) {
        return rv.text.substring(0, 97)+"...";
      }


      if (typeof field.options != "undefined") if(cv!==null) {
        if (typeof field.options[cv] != "undefined") {
          if(field.option_colors && field.option_colors[cv]) {
            return '<span class="g-badge" style="background:'+field.option_colors[cv]+'">'+field.options[cv]+'</span>';
          }
          return field.options[cv]
        }
        let resp = ''
        let csv = cv.split(',')
        for(i=0;i<csv.length;i++)  if(typeof field.options[csv[i]] != "undefined") {
          resp += field.options[csv[i]]+'<br>'
        } else resp += csv[i]+'<br>'
        return resp
      }

      if(field.inline_edit) {
        return '<div contenteditable="true" data-field="'+fkey+'">'+displayValue+'</div>';
      }
      return displayValue;
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
      if(this.order[key]=='DESC') order='ASC'; else order='DESC';
      this.order = []
      this.order[key] = order
      this.load_page({page:1})
    },
    sortiClass: function(key){
      cl=''
      if(this.order[key]=='ASC') cl='fa-chevron-up'
      if(this.order[key]=='DESC') cl='fa-chevron-down'
      return 'g-sorti fa '+cl;
    },
    thStyle: function(key){
      style = '';
      if(this.table.fields[key].width) {
        style += 'width:'+this.table.fields[key].width+';'
      } else {
        style += 'width:min-content;'
      }
      if(this.table.fields[key].type && this.table.fields[key].type=='number') {
        style += 'text-align:right;'
      }
      return style;
    },
    checkboxClass: function(irow){
      cl = ''
      if(this.selectedRow(irow)) cl='fa-check-square-o'; else cl='fa-square-o';
      return 'fa tr_checkbox '+cl;
    },
    selectedRow: function(irow){
      return this.selected_rows.indexOf(irow)>-1;
    },
    checkboxClassBulk: function(){
      cl = 'fa-square-o'
      if(this.bulk_selected>0) cl='fa-check-square-o';
      if(this.bulk_selected<0) cl='fa-minus-square-o';
      return 'fa bulk_checkbox '+cl;
    },
    inlineDataUpdate: function(irow, fkey){
      if(!this.table.fields[fkey].inline_edit) return;
      this.updateRows.push(irow);
    }
  },
  mounted: function() {
    if(this.gtotalrows) {
      this.data.totalRows = parseInt(this.gtotalrows)
    }
    if(this.grows) {
      this.data.rows = JSON.parse(this.grows)
      if(typeof lazyImgLoad!='undefined') {
        setTimeout(function(){lazyImgLoad();}, 150);
      }
    }
    if(this.data.rows.length==0) this.load_page({page:1})
    rootVueGTables.push(this)

    this.intervalUpdate = setInterval(function(_this){
      for(i=0; i<_this.updateRows.length; i++) {
        irow = _this.updateRows[i]
        row = _this.data.rows[irow];
        id = row[0];
        data = {}
        tr = g('tr[row-id="'+irow+'"] [contenteditable="true"]').all
        for(j=0; j<tr.length; j++) {
          field = tr[j].getAttribute('data-field')
          value = tr[j].innerHTML
          data[field] = value
        }

        url = 'cm/update_rows/'+_this.name+'?id='+id
        g.ajax({method:'post',url:url,data:data,fn:function(data) {
          console.log("Saved #"+id);
          tr = g('tr[row-id="'+irow+'"] td').all
          for(j=0; j<tr.length; j++) {
            tr[j].animate([{background:'lightgreen'},{background:'inherit'}], {duration:600})
          }
        }})

      }
      _this.updateRows = [];
    }, 3000, this);
  }
})

function transformClassComponents() {
  let textareas
  textareas=g('.codemirror-js').all
  cmirror=[]
  for(i=0;i<textareas.length;i++) {
    x=textareas[i].name
    cmirror[x]=CodeMirror.fromTextArea(textareas[i],{lineNumbers:true,mode:'javascript'});
  }
  
  textareas=g('.tinymce').all
  mce_editor=[]
  if(tinymce) tinymce.remove() //remove all tinymce editors
  for(i=0;i<textareas.length;i++) {
    mce_editor[i] = {id: textareas[i].id, name: textareas[i].name};
    mce_editor[i].settings = JSON.parse(JSON.stringify(g_tinymce_options));
    mce_editor[i].settings.selector = '[name='+textareas[i].name.replace('[','\\[').replace(']','\\]')+']'
    mce_editor[i].settings.file_picker_callback = function(cb, value, meta) {
      input_filename = cb;
      open_gallery_post();
    }
    tinymce.init(mce_editor[i].settings)
  }

  if(typeof $ != 'undefined' && typeof $.fn.select2 != 'undefined') $(".select2").select2();
}

function readFromClassComponents() {
  let values = new Array()
  for (x in mce_editor)  {
    values[mce_editor[x].name] = tinymce.get(mce_editor[x].id).getContent()
  }
  textareas=g('.codemirror-js').all
  for (x in cmirror) {
    values[x] = cmirror[x].getValue()
  }
  return values
}

gtableCommand = Array()
gtableTool = Array()
gtableFieldDisplay = Array()

gtableCommand['edit'] = {
  fa: "pencil",
  label: "Edit",
  fn: function(table,irow){
    let _this = table
    _this.edititem = irow
    _this.edit_html = "Loading..."
    g.loader()
    g.get('cm/edit_form/'+_this.name+'?id='+irow,function(data){
      _this.edit_html = data
      g.loader(false)
      app.$forceUpdate()
    })
  }
}

gtableCommand['edit_page'] = {
  fa: "pencil",
  label: "Edit",
  fn: function(table,irow){
    window.location.href = 'admin/content/'+table.name+'/'+irow
  }
}

gtableCommand['edit_popup'] = {
  fa: "pencil",
  label: "Edit",
  fn: function(table,irow) {
  href='cm/edit_form/'+table.name+'?id='+irow+'&callback=g_form_popup_update';
    g.get(href,function(data){
      g.dialog({title:g.tr('Edit Registry'), class:'lightscreen large',body:data,type:'modal',buttons:'popup_update'})
      formId = '#'+table.name+'-edit-item-form'
      edit_popup_app = new Vue({
        el: formId,
        data: {id:irow}
      })
      transformClassComponents()
      console.log(formId+' input')
      g(formId+' input').all[1].focus()
    })
  }
}

g.dialog.buttons.popup_update = {title:'Update', fn:function(btn){
  form = g('.gila-popup form').last()
  form.getElementsByTagName("BUTTON")[0].click()
}};

function g_form_popup_update() {
  form = g('.gila-popup form').last()
  data = new FormData(form);
  t = form.getAttribute('data-table')
  id = form.getAttribute('data-id')
  for(i=0; i<rootVueGTables.length; i++) if(rootVueGTables[i].name == t) {
    _this = rootVueGTables[i]
  }

  if(id=='new'||id==0) {
    url = 'cm/update_rows/'+t
    if(typeof _this.filters!='undefined') {
      url = url+'?'+_this.filters
    }
  } else {
    url = 'cm/update_rows/'+t+'?id='+id
  }
  g.ajax({method:'post',url:url,data:data,fn:function(data) {
    data = JSON.parse(data)
    if(id=='new' || id==0) {
      _this.data.rows.unshift(data.rows[0])
      edit_popup_app.id = _this.data.rows[0][0]
      if(typeof _this.table.children!='undefined') setTimeout(function(){
        document.getElementById("edit_popup_child").scrollIntoView();
      }, 100)
    } else {
      _this.update_row(data.rows[0])
    }
  }})

  if((id=='new'||id==0) && typeof _this.table.children!='undefined') {
    return
  }

  g.closeModal();
} 


gtableCommand['clone'] = {
  fa: "copy",
  label: "Clone",
  fn: function(table,id) {
    let _this
    _this = table
    _this.edit_html = "Loading..."
    g.post('cm/insert_row/'+_this.name, 'id='+id+'&formToken='+csrfToken, function(data){
      data = JSON.parse(data)
      _this.data.rows.unshift(data.rows[0])
    })
  }
}

gtableCommand['delete'] = {
  fa: "trash-o",
  label: "Delete",
  fn: function(table,id) {
    let _this = table
    let _id = id
    data = new FormData()
    data.append('id',id)
    data.append('formToken',csrfToken)
    if(confirm(_e("Delete registry?"))) g.ajax({
      url: "cm/delete?t="+_this.name,
      data: data,
      method: 'post',
      fn: function(data) {
        for(i=0;i<_this.data.rows.length;i++) if(_this.data.rows[i][0] == _id) {
          _this.data.rows.splice(i,1)
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
    g.get('cm/edit_form/'+_this.name, function(data){
      _this.edit_html = data
    })
  }
}
gtableTool['add_row'] = {
  fa: "plus", label: _e("New"),
  fn: function(table) {
    let _this
    _this = table
    _this.edit_html = _e("Loading")+"..."
    g.post('cm/insert_row/'+_this.name, _this.query,function(data){
      data = JSON.parse(data)
      if(typeof _this.data.rows=='undefined') {
        _this.data.rows = [data.rows[0]]
      } else _this.data.rows.unshift(data.rows[0])
    })
  }
}
gtableTool['add_popup'] = {
  fa: "plus", label: _e("New"),
  fn: function(table) {
    href='cm/edit_form/'+table.name+'?callback=g_form_popup_update';
    g.get(href,function(data){
      g.dialog({title:g.tr('New Registry'), class:'lightscreen large',body:data,type:'modal',buttons:'popup_update'})
      formId = '#'+table.name+'-edit-item-form'
      edit_popup_app = new Vue({
        el: formId,
        data: {id:0}
      })
      transformClassComponents()
      g(formId+' input').all[1].focus()
    })
  }
}
gtableTool['csv'] = {
  fa: "arrow-down", label: "Csv",
  fn: function(table) {
    window.location.href = 'cm/csv/'+table.name+'?'+table.query;
  }
}
gtableTool['log_selected'] = {
  fa: "arrow-down", label: "Log",
  fn: function(table) {
    console.log(table.selected_rows);
  }
}
gtableTool['delete'] = {
  fa: "arrow-down", label: _e("Delete"),
  fn: function(table) {
    let _this = table
    if(confirm(_e("Delete registries?"))) g.ajax({
      url: "cm/delete?t="+_this.name,
      data: {id:table.selected_rows.join()},
      method: 'post',
      fn: function(data) {
        _this.selected_rows = []
        _this.load_page()
      }
    });
  }
}
gtableTool['uploadcsv'] = {
  fa: "arrow-up", label: _e("Upload")+" CSV",
  fn: function(table) {
    bodyMsg = "<h3>1. "+_e('_uploadcsv_step1')+'</h3>'
    bodyMsg += " <a href='cm/get_empty_csv/"+table.name+"'>"+_e('Download')+"</a>"
    bodyMsg += "<h3>2. "+_e('_uploadcsv_step2')+'</h3>'
    bodyMsg += "<br><input type='file' id='g_file_to_upload' data-table='"+table.name+"'>"
    bodyMsg += "<h3>3. "+_e('_uploadcsv_step3')+'</h3>'
    bodyMsg += " <span class='g-btn' onclick='upload_csv_file()'>"+_e('Upload')+"</span>"
    g.dialog({title:_e("Upload")+" CSV", body:bodyMsg, buttons:'',type:'modal', class:'large', id:'select_row_dialog'})
  }
}
gtableTool['upload_csv'] = gtableTool['uploadcsv']
gtableTool['addfrom'] = {
  fa: "plus", label: _e("New from"),
  fn: function(table) {
    let _table
    _table = table.table
    g.post('cm/select_row/'+_table.tool.addfrom[0],
      "list="+_table.tool.addfrom[1]+'&formToken='+csrfToken, function(gal){
      g.dialog({title:_e("Select"),body:gal,buttons:'select_row_source',type:'modal',class:'large',id:'select_row_dialog'})
      app.table_to_insert = _table.name
    })
  }
}
gtableTool['approve'] = {
  fa: "check", label: _e("Approve"),
  fn: function(table) {
    if(typeof table.table.approve=='undefined') {
      alert('table[approve] is not set')
      return
    }
    let _this = table
    data = {}
    data[table.table.approve[0]] = table.table.approve[1]
    g.ajax({
      url: "cm/update_rows?t="+_this.name+'&id='+table.selected_rows.join(),
      data: data,
      method: 'post',
      fn: function(data) {
        _this.selected_rows = []
        _this.load_page()
      }
    });
  }
}

g_tinymce_options = {
  selector: '',
  relative_urls: false,
  remove_script_host: false,
  height: 300,
  remove_linebreaks : false,
  document_base_url: ".",
  verify_html: false,
  cleanup: true,
  plugins: ['code codesample table charmap image media lists link emoticons'],
  menubar: true,
  entity_encoding: 'raw',
  toolbar: 'formatselect bold italic | bullist numlist outdent indent | link image table | alignleft aligncenter alignright alignjustify',
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
    g.closeModal('media_dialog');
  }
}
g.dialog.buttons.select_path_post = {
  title:'Select', fn: function() {
    let v = g('#selected-path').attr('value')
    if(v!=null) input_filename(base_url+v);
    g.closeModal('media_dialog');
  }
}
g.dialog.buttons.select_row_source = {
  title:'Select', fn: function() {
    let v = g('#selected-row').attr('value')
    alert(v);
    g('#select_row_dialog').parent().remove();
  }
}

function open_gallery_post() {
  g.post("admin/media","g_response=content"+'&formToken='+csrfToken,function(gal){ 
    g.dialog({title:"Media gallery",body:gal,buttons:'select_path_post',type:'modal',class:'large',id:'media_dialog','z-index':99999})
  })
}
function open_select_row(row,table,name) {
  input_select_row = row;
  g.post("cm/select_row/"+table,"",function(gal){
    g.dialog({title:_e(name),body:gal,buttons:'select_row_source',type:'modal',id:'select_row_dialog',class:'large'})
  })
}
function upload_csv_file() {
  let fm = new FormData()
  fm.append('file', g.el('g_file_to_upload').files[0]);
  table = g.el('g_file_to_upload').getAttribute('data-table');
  g.loader()
  g.ajax({url:"cm/upload_csv/"+table, method:'POST', data:fm, fn:function(data){
    app.$refs.gtable.load_page()
    g.loader(false)
    g('.gila-darkscreen').remove();
  }})
}

g.click(".select-row",function(){
  g('.select-row').removeClass('g-selected');
  g(this).addClass('g-selected');
  g('#selected-row').attr('value',this.getAttribute('data-id'))
})
