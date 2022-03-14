
var ve_node_style = {
  IMG:{'width':[],'height':[]},
  SPAN:{'color':[],'font-family':[],'font-size':[]}
}

var ve_node_attr = {
  IMG:{'src':[]},
  A:{'href':'#','target':[]}
}

var mydata = {
  buttons_def: {
    bold:{label:"<i class='fa fa-bold'></i>",action:"setNode",args:'B'},
    italian:{label:"<i class='fa fa-italic'></i>",action:"setNode",args:'I'},
    del:{label:"<i class='fa fa-strikethrough'></i>",action:"setNode",args:'del'},
    ins:{label:"<i class='fa fa-underline'></i>",action:"setNode",args:'INS'},
    sub:{label:"<i class='fa fa-subscript'></i>",action:"setNode",args:'SUB'},
    sup:{label:"<i class='fa fa-superscript'></i>",action:"setNode",args:'SUP'},
    blockquote:{label:"<i class='fa fa-quote-left'></i>",action:"setNode",args:'BLOCKQUOTE'},
    link:{label:"<i class='fa fa-link'></i>",action:"setNode",args:['A',null,{href:''}]},
    code:{label:"<sup>+</sup><i class='fa fa-code'></i>",action:"insertNode",args:['PRE','<code><br></code>']},
    h1:{label:"<i class='fa fa-header'></i>",action:"setNode",args:'H1'},
    h2:{label:"<i class='fa fa-header'></i><sub>2</sub>",action:"setNode",args:'H2'},
    h3:{label:"<i class='fa fa-header'></i><sub>3</sub>",action:"setNode",args:'H3'},
    h4:{label:"<i class='fa fa-header'></i><sub>4</sub>",action:"setNode",args:'H4'},
    ul:{label:"<sup>+</sup><i class='fa fa-list-ul'></i>",action:"insertNode",args:['UL','<li>Item',true]},
    ol:{label:"<sup>+</sup><i class='fa fa-list-ol'></i>",action:"insertNode",args:['OL','<li>Item',true]},
    newline:{label:"<sup>+</sup>nl",action:"append",args:'<br>'},
    unset:{label:"T<sub>x</sub>",action:"unsetNode",args:['B','I','DEL','INS','SUB','SUB','SUP','BLOCKQUOTE']},
    aleft:{label:"<i class='fa fa-align-left'></i>",action:"setStyle",args:['text-align','left']},
    amiddle:{label:"<i class='fa fa-align-center'></i>",action:"setStyle",args:['text-align','center']},
    ajustify:{label:"<i class='fa fa-align-justify'></i>",action:"setStyle",args:['text-align','justify']},
    aright:{label:"<i class='fa fa-align-right'></i>",action:"setStyle",args:['text-align','right']},
    fontb:{label:"<i class='fa fa-bold'></i>",action:"setStyle",args:['fontWeight','bold']},
    //img:{label:"<i class='fa fa-image'></i>",action:"insertNode",args:['FIGURE','<img src="img.jpg" ><figcaption>Image Caption</figcaption>',false]},
    table:{label:"<i class='fa fa-table'></i>",action:"insertNode",args:['TABLE','<tr><td><td><td><tr><td><td><td><tr><td><td><td>']},
  },
  buttons_i:['bold','italian','blockquote','link','del','ins','sup','sub',
  'h1','h2','h3','ul','ol','code','newline','unset'], //,'aleft','amiddle','aright'
  node_buttons:{
  },
  figure:[],
  elpath:[],
  node2edit: false,
  nodeobj: [],
  areaID:'',
  previous_endOffset:0,
  content: this.text
}

Vue.component('vue-editor', {
  template: '<div class="ve-editor">\
  <div class="ve-editor-bar">\
    <span class="ve-editor-btn" v-for="btn in buttons_i" @mousedown="btnAction(btn)"\
     :index="btn" v-html="buttons_def[btn].label"></span>\
  </div>\
  <input v-model="content" type="hidden" :name="name" >\
  <div style="position:relative">\
    <div contenteditable="true" ref="text" :id="areaID" @click="onclick($event)"\
      @keydown="keydown($event)" class="ve-editor-area" @input="update" v-html="text">\
    </div>\
  </div>\
  <div v-if="node2edit!=false" class="ve-editor-edit">\
    <{{node2edit.nodeName}}>\
    <span class="ve-editor-btn" @click="unsetEditNode">Unset</span>\
    <span class="ve-editor-btn" @click="deleteEditNode">Delete</span>\
    <span v-for="(value,key) in nodeobj">\
      &nbsp;<input v-model="nodeobj[key]" @input="updateEditNode"\
      :placeholder="key">\
    </span>\
  </div>\
  </div>',

  data: function(){ mydata.content=this.text; return mydata; },
  props: ['buttons','text','name'],
  created: function () {
    if(typeof this.buttons!='undefined') this.buttons_i=this.buttons.split(' ')
    do{
      this.areaID = 'g-editor-area-'+Math.floor(Math.random()*100000)
    }while(document.getElementById(this.areaID))
  },
  methods: {
    update: function() {
      this.content = this.$refs["text"].innerHTML;
    },
    btnAction: function(index) {
      action = this.buttons_def[index].action
      args = this.buttons_def[index].args
      switch(action) {
        case 'setNode':
          if(Array.isArray(args)) {
            this.setNode(args[0],args[1],args[2])
          } else {
            this.setNode(args)
          }
        break
        case 'unsetNode':
        this.unsetNode(args)
        break
        case 'setStyle':
        this.setStyle(args[0],args[1])
        break
        case 'setAttr':
        this.setAttr(args[0],args[1])
        break
        case 'append':
        this.append(args)
        break
        case 'insertNode':
        this.insertNode(args[0],args[1],args[2],args[3])
        break
      }
    },
    setNode: function (x,html='',obj=null) {
      if(!this.onEditor()) return
      if(this.sel.anchorNode.parentNode.nodeName==x) {
        return
      }

      if(getSelection()=='') {
        if(html=='') return
        this.insertNode(x,html,true,obj)
        return
      }

      var el = document.createElement(x);
      el.innerHTML = getSelection();
      this.range.deleteContents();
      this.insert(el)
      if(obj) for(attr in obj) el[attr]=obj[attr]
    },
    setStyle: function (attr,value) {
      pN=this.sel.anchorNode
      if(pN.nodeType==3) pN=pN.parentNode
      if(pN.style[attr]==value) {
        pN.style[attr]=''
        return
      }
      pN.style[attr]=value
    },
    setAttr: function (attr,value) {
      pN=this.sel.anchorNode
      if(pN.nodeType==3) pN=pN.parentNode
      if(pN.hasAttribute(attr)) if(pN.getAttribute(attr)==value) {
        pN.removeAttribute(attr)
        return
      }
      pN.setAttribute(attr,value)
    },
    append: function (value) {
      document.getElementById(this.areaID).innerHTML+=value
      this.update();
    },
    insertNode: function (x,html=' ',editable=true,obj=null) {
      if(!this.onEditor()) return
      var el = document.createElement(x);
      el.innerHTML = html;
      this.insert(el)
      if(obj) for(attr in obj) el[attr] = obj[attr]
      if(editable) {
        this.range.selectNodeContents(el)
      }
    },
    insertText: function (html) {
      if(!this.onEditor()) return
      var el = document.createTextNode(html);
      this.insert(el)
    },
    insert: function(el) {
      this.range.insertNode(el);
      this.range.setStartAfter(el)
      this.sel.removeAllRanges()
      this.sel.addRange(this.range)
      this.update();
    },
    unsetNode: function (x){
      if(!this.onEditor()) return
      if(!Array.isArray(x)) x=[x]

      if (!x.includes(this.sel.anchorNode.parentNode.nodeName)) return
      var el = this.sel.anchorNode.parentNode
      var parent = el.parentNode;
      while( el.firstChild ) {
        parent.insertBefore(  el.firstChild, el );
      }
      parent.removeChild(el);
      this.update();
    },
    editNodeIndx: function (indx) {
      this.editNode(this.elpath[this.elpath.length-indx-1])
    },
    editNode: function (node) {
      this.node2edit = node
      this.nodeobj = {'class':node.className,id:node.id}
      if(node.nodeName=='IMG') this.nodeobj.src = node.src
      let ve = ve_node_style[this.node2edit.nodeName]
      if(ve) for(style in ve) {
        this.nodeobj[style] = node.style[style]
        if(style=='align') this.nodeobj[style] = node.getAttribute(style)
      }
      let va = ve_node_attr[this.node2edit.nodeName]
      if(va) for(attr in va) {
        this.nodeobj[attr] = node[attr]
      }
      this.update();
    },
    updateEditNode: function () {
      this.node2edit.className = this.nodeobj.class
      this.node2edit.id = this.nodeobj.id
      let ve = ve_node_style[this.node2edit.nodeName]
      if(this.node2edit.nodeName=='IMG') this.node2edit.src = this.nodeobj.src
      if(ve) for(style in ve) {
        if(style=='align') {
          this.node2edit.setAttribute(style,this.nodeobj[style])
        } else this.node2edit.style[style] = this.nodeobj[style]
      }
      let va = ve_node_attr[this.node2edit.nodeName]
      if(va) for(attr in va) {
        this.node2edit[attr] = this.nodeobj[attr]
      }
      this.update();
    },
    deleteEditNode: function () {
      res = confirm('Remove node and its components?')
      if(res == true) {
        this.node2edit.parentNode.removeChild(this.node2edit)
        this.node2edit = false
      }
      this.update();
    },
    unsetEditNode: function () {
      var el = this.node2edit
      var parent = el.parentNode;
      while( el.firstChild ) {
        parent.insertBefore(  el.firstChild, el );
      }
      parent.removeChild(el);
      this.node2edit = false
      this.update();
    },
    exitEditNode: function () {
      this.node2edit = false
    },
    figureEdit: function(obj) {
      this.figure=obj
    },
    onEditor: function () {
      el = document.getElementById(this.areaID)
      if (window.getSelection) {
        this.sel = window.getSelection();

        if (this.sel.rangeCount > 0 && this.sel.getRangeAt) {
          for (var i = 0; i < this.sel.rangeCount; ++i) {
            if (!this.isOrContains(this.sel.getRangeAt(i).commonAncestorContainer, el)) {
              return false;
            }
          }

          this.range = this.sel.getRangeAt(0);
          this.node2edit = false
          // clean up script,p and div tag
          all = el.getElementsByTagName("DIV");
          for (let i=all.length-1, min=-1; i>min; i--) {
            anchor = all[i]
            if(typeof anchor!=='undefined' && anchor!=el &&
                typeof anchor.parentNode!=='undefined' && anchor.parentNode!=el.parentNode) {
              while(anchor.firstChild) {
                anchor.parentNode.insertBefore(anchor.firstChild, anchor);
              }
              anchor.parentNode.removeChild(anchor);
            }
          }

          return true;
        }
      }

      return false;
    },
    isOrContains: function (node, container) {
      while (node) {
        if (node === container) return true;
        node = node.parentNode;
      }
      return false;
    },
    onclick: function(event) {
      if(!this.onEditor()) return
      this.node2edit=false;
      if(event.target.id!=this.areaID) this.editNode(event.target)
    },
    keydown: function(event) {
      if(!this.onEditor()) return
      if(event.keyCode==13) {
        nodeName = this.sel.anchorNode.parentNode.nodeName;
        if(['LI'].includes(nodeName)) return;
        if(['OL','UL','PRE','CODE','A','B','I','BLOCKQUOTE'].includes(nodeName)) return;
        document.execCommand('insertHTML', false, '<br><br>');
        event.preventDefault();
      }
    }
  }

})
