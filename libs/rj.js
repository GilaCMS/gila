
var css ="*{box-sizing: border-box;margin:0;vertical-align:top}div,p,form{padding:8px;}[class^='col-']{display:inline-table}"
css += 'label,input,form{display:block;width:100%;box-sizing: border-box}'
css += '.box-shadow{box-shadow: 2px 2px 2px #888888}.border{border: 1px solid #888}'
css += 'input{margin-bottom:8px;padding:4px}'
//display-flex
for(i=1; i<13; i++){
	css += '.col-'+i+'{width:'+(i/0.12)+'%}\n'
}

var sc = {
margin: ['auto','0','4px','8px','16px'],
display: ['inline','flex','block','8px','16px'],
'align-items': ['center','flex-start','flex-end']

}

for(i in sc) for(j=0; j<sc[i].length; j++) css += '.'+i+'-'+sc[i][j]+'{'+i+':'+sc[i][j]+'}\n'


var style = document.createElement('style');
style.type = 'text/css';
if (style.styleSheet){
  style.styleSheet.cssText = css+style.styleSheet.cssText
} else {
  style.appendChild(document.createTextNode(css))
}

document.head.appendChild(style);


/*
document.addEventListener('DOMContentLoaded', function() {
    var p = document.createElement("p");
    var content = document.createTextNode(" more text");
    p.appendChild(content);
    document.body.appendChild(p);

});
*/
var rj_query=""
var rj_item=document;

function rjItem(){
    this.domlist = {}
    this.data = {}
    this.domlist[0] = document
}


function rj(q){
	if (typeof q === 'undefined') return new rjItem()

	if (q === rj_query)
		return rj_item;
    else
		 var rj_item = new rjItem()

    if (typeof q === 'object'){
		rj_item[0] = q
	}else{
		rj_item.domlist = document.body.querySelectorAll(q)
    }
    return rj_item
    //d.forEach(function*(e){yield e})

}


rjItem.prototype.html = function (html){

    for(let value of this.domlist){
        value.innerHTML = html
    }
    return this
}

rjItem.prototype.attr = function (attr,val){
    if (typeof val === 'undefined') return value[attr]
    for(let value of this.domlist){
        value.setAttribute(attr, val)
    }

    return this
}
rjItem.prototype.style = function (attr,val){
    if (typeof val === 'undefined') return value.style[attr]
    for(let value of this.domlist){
        value.style[attr] = val
    }

    return this
}


rjItem.prototype.append = function (html,data,data_timeout){

    for(let value of this.domlist){
        let template = document.createElement('template');
        template.innerHTML = html;
        value.appendChild(template.content.firstChild)

        if (typeof data !== 'undefined') {
			for(let attr in data){
				template.content.firstChild[attr] = data[attr]
			}
		}

        if (typeof data !== 'undefined') setTimeout(function () {
			for(let attr in data){
				template.content.firstChild[attr] = data[attr]
			}
		}, 100)

    }
    return this
}

rjItem.prototype.createNS = function (node,data,data_timeout){

    for(let value of this.domlist){
        let child = document.createElementNS("http://www.w3.org/2000/svg", node);

        //if (typeof data !== 'undefined') {
			for(let attr in data) {
				child.setAttribute(attr,data[attr])
			}
		//}

		value.appendChild(child)

        if (typeof data_timeout !== 'undefined') setTimeout(function () {
			for(let attr in data_timeout) {
				child.setAttribute(attr,data_timeout[attr])
			}
		}, 100)


    }

    return this
}


 /*
rjItem.prototype.node = function (node,data){
    if (typeof data !== 'undefined') {
		html = '<'+node+''
		for(let attr in data) html += ' '+attr+'="'+data[attr]+'"'
		html += '/>'
	} else html = node
	console.log(html)
    for(let value of this.domlist){
        var template = document.createElement('template');
        template.innerHTML = html;
        console.log(html)
        value.appendChild(template.content.firstChild)
    }
    return this
}
rjItem.prototype.create = function (node,data){

    for(let value of this.domlist){
		var nnode = document.createElement(node)
		if (typeof data !== 'undefined') for(let attr in data){
			nnode[attr] = data[attr]
		}
        value.appendChild(nnode)
    }
    return this
}*/


// Prototypes

Array.prototype.max = function() {
  return Math.max.apply(null, this);
};

Array.prototype.min = function() {
  return Math.min.apply(null, this);
};
