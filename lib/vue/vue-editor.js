


var ve_node_style={
	IMG:{'width':[],'height':[]},
	SPAN:{'color':[],'font-family':[],'font-size':[]}
}

var ve_node_attr={
	IMG:{'src':[]},
	A:{'href':'#','target':[]}
}

var mydata={
	buttons_def:{
		bold:{label:"<i class='fa fa-bold'></i>",action:"setNode",args:'B'},
		italian:{label:"<i class='fa fa-italic'></i>",action:"setNode",args:'I'},
        h1:{label:"<i class='fa fa-header'></i>",action:"setNode",args:'H1'},
        h2:{label:"<i class='fa fa-header'></i><sub>2</sub>",action:"setNode",args:'H2'},
        h3:{label:"<i class='fa fa-header'></i><sub>3</sub>",action:"setNode",args:'H3'},
        h4:{label:"<i class='fa fa-header'></i><sub>4</sub>",action:"setNode",args:'H4'},
		del:{label:"<i class='fa fa-strikethrough'></i>",action:"setNode",args:'DEL'},
		ins:{label:"<i class='fa fa-underline'></i>",action:"setNode",args:'INS'},
		sub:{label:"<i class='fa fa-subscript'></i>",action:"setNode",args:'SUB'},
		sup:{label:"<i class='fa fa-superscript'></i>",action:"setNode",args:'SUP'},
		unset:{label:"T<sub>x</sub>",action:"unsetNode",args:['B','I','DEL','INS','SUB','SUB','SUP','BLOCKQUOTE']},
		ul:{label:"<i class='fa fa-list-ul'></i>",action:"insertNode",args:['UL','<li> ',true]},
		ol:{label:"<i class='fa fa-list-ol'></i>",action:"insertNode",args:['OL','<li> ',true]},
		pre:{label:"<i class='fa fa-code'></i>",action:"insertNode",args:['PRE','<code> ',true]},
        blockquote:{label:"<i class='fa fa-quote-left'></i>",action:"setNode",args:'BLOCKQUOTE'},
        //link:{label:"<i class='fa fa-link'></i>",action:"insertNode",args:['A','[link]',true,{href:'#'}]},
        link:{label:"<i class='fa fa-link'></i>",action:"setNode",args:['A','link',{href:'#'}]},
        aleft:{label:"<i class='fa fa-align-left'></i>",action:"setStyle",args:['text-align','left']},
        amiddle:{label:"<i class='fa fa-align-center'></i>",action:"setStyle",args:['text-align','center']},
        ajustify:{label:"<i class='fa fa-align-justify'></i>",action:"setStyle",args:['text-align','justify']},
        aright:{label:"<i class='fa fa-align-right'></i>",action:"setStyle",args:['text-align','right']},
        fontb:{label:"<i class='fa fa-bold'></i>",action:"setStyle",args:['fontWeight','bold']},
        img:{label:"<i class='fa fa-image'></i>",action:"insertNode",args:['FIGURE','<img src="img.jpg" ><figcaption>Image Caption</figcaption>',false]},
        table:{label:"<i class='fa fa-table'></i>",action:"insertNode",args:['TABLE','<tr><td><td><td><tr><td><td><td><tr><td><td><td>']},
        code:{label:"<i class='fa fa-code'></i>",action:"insertNode",args:['PRE','<code><br></code>']},
        p:{label:"<b>P</b>",action:"append",args:'<p><br></p>'},
        div:{label:"<b>DIV</b>",action:"append",args:'<div><br></div>'},
		save:{label:"<i class='fa fa-save'></i>",action:"saveHtml"}
	},
	buttons_i:['bold','italian','h1','h2','h3','blockquote','unset','ul','ol','aleft','amiddle','aright','link','img','code','p','div'],
    node_buttons:{

    },
	figure:[],
	elpath:[],
	node2edit: false,
	nodeobj: [],
	areaID:'',
    previous_endOffset:0
}

Vue.component('vue-editor', {
	template: '<div class="ve-editor">\
	<div class="ve-editor-bar"><button v-for="btn in buttons_i" @click="btnAction(btn)" :index="btn" v-html="buttons_def[btn].label"></button></div>\
	<div style="position:relative">\
		<div contenteditable="true" :id="areaID" v-on:click="onclick" v-on:keydown="onkeydown" class="ve-editor-area" v-html="text"></div>\
		<div class="ve-edit-node">\
			<table v-if="node2edit!=false" >\
                <tr><th>{{node2edit.nodeName}}</th><td><button v-on:click="unsetEditNode">Unset</button></td>\
                <tr v-for="(value,key) in nodeobj">\
                <th>{{ key }}&nbsp;</th>\
                <td><input v-model="nodeobj[key]" v-on:input="updateEditNode"></td>\
                </tr>\
                <tr><td></td><td><button v-on:click="deleteEditNode">Del</button></td></tr>\
            </table>\
        </div>\
	</div>\
	</div>',
  	data: function(){ return mydata },
	props: ['buttons','text'],
	created: function () {
		if(typeof this.buttons!='undefined') this.buttons_i=this.buttons.split(' ')

		do{
			this.areaID = 'g-editor-area-'+Math.floor(Math.random()*100000)
		}while(document.getElementById(this.areaID))

	},
	methods: {
		btnAction: function(index) {
			action = this.buttons_def[index].action
			args = this.buttons_def[index].args
			switch(action) {
				case 'setNode':
				this.setNode(args[0],args[1],args[2])
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
				case 'saveHtml':
				this.saveHtml()
				break
			}

		},
		setNode: function (x,html='',obj=null) {
			x=x.toUpperCase()
			if(!this.onEditor()) return
			if(this.sel.anchorNode.parentNode.nodeName==x) {
				this.unsetNode(x)
				return
			}

            if(getSelection()=='') {
				this.insertNode(x,x,true,obj)
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

			if(pN.hasAttribute(attr)) if(pN.getAttribute(attr)==value){
                pN.removeAttribute(attr)
                return
            }

            pN.setAttribute(attr,value)
		},
        append: function (value) {
            document.getElementById(this.areaID).innerHTML+=value
		},
		insertNode: function (x,html=' ',editable=true,obj=null) {
			x=x.toUpperCase()
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
		},
		updateEditNode: function () {
			this.node2edit.className = this.nodeobj.class
			this.node2edit.id = this.nodeobj.id
            if(this.node2edit.nodeName=='IMG') this.node2edit.src = this.nodeobj.src
			let ve = ve_node_style[this.node2edit.nodeName]
			if(ve) for(style in ve) {
                if(style=='align') {
                    this.node2edit.setAttribute(style,this.nodeobj[style])
                } else this.node2edit.style[style] = this.nodeobj[style]
			}
			let va = ve_node_attr[this.node2edit.nodeName]
			if(va) for(attr in va) {
				this.node2edit[attr] = this.nodeobj[attr]
			}
		},
		deleteEditNode: function () {
			res = confirm('Remove node and its components?')
			if(res == true) {
				this.node2edit.parentNode.removeChild(this.node2edit)
                this.node2edit = false
			}
		},
		unsetEditNode: function () {
			var el = this.node2edit
			var parent = el.parentNode;
			while( el.firstChild ) {
				parent.insertBefore(  el.firstChild, el );
			}
			parent.removeChild(el);
            this.node2edit = false
		},
		exitEditNode: function () {
			this.node2edit = false
		},
		figureEdit: function(obj) {
			this.figure=obj
		},
		saveHtml: function() {
			console.log(document.getElementById(this.areaID).innerHTML)
		},
		onEditor: function () {
			el = document.getElementById(this.areaID)
			if (window.getSelection) {
				this.sel = window.getSelection();
				this.findElPath()

				if (this.sel.rangeCount > 0 && this.sel.getRangeAt) {
					for (var i = 0; i < this.sel.rangeCount; ++i) {
						if (!this.isOrContains(this.sel.getRangeAt(i).commonAncestorContainer, el)) {
							return false;
						}
					}

					this.range = this.sel.getRangeAt(0);
					this.node2edit = false
					return true;

				}
			}

			return false;
		},
		findElPath: function() {
            return
			this.elpath = new Array()
            this.elpath[0] = this.sel.parentNode
            return
			i=0
			if(this.sel.id == this.areaID) return
			el = this.sel.anchorNode
			if(el.id == this.areaID) return
			if(el) el=el.parentNode; else el=this.sel.parentNode
            el=this.sel.parentNode;
			while(el) {
				if(el.id == this.areaID) return
				this.elpath[i] = el
				i++
				el = el.parentNode
			}
		},
		isOrContains: function (node, container) {
			while (node) {
				if (node === container) return true;
				node = node.parentNode;
			}
			return false;
		},
		onclick: function() {
			if(!this.onEditor()) return
            this.node2edit=false;
            if(event.target.id!=this.areaID) this.editNode(event.target)
		},
		onkeydown: function() {
            if(!this.onEditor()) return
            this.node2edit=false;
            if(event.target.id!=this.areaID) this.editNode(event.target)
		},
		uncopy: function(event) {
			return
    		if (event.keyCode == 13) {
				sel = window.getSelection();
				pNodes = ['P','LI']

        		if (pNodes.includes(sel.anchorNode.nodeName)) return
        		if ((sel.anchorNode.nodeName=='#text') && pNodes.includes(sel.anchorNode.parentNode.nodeName)) return
				document.execCommand('insertHTML', false, '<br>');
				if ((sel.anchorNode.nodeName!='#text')) this.unsetNode(sel.anchorNode.nodeName)
				return false;
    		}
  		}
	}

})
