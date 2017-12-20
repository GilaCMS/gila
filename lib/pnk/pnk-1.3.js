
var PNK = {};
var lang_array;
if (typeof pnk_src == "undefined") pnk_src = []
PNK.path = "pnk/"
PNK.classes = {}
PNK.field_types = {
	number: {
		tdclass:'col-number',
		cv: function(fc,cv){
			if(typeof numeral == 'function') {
				format=isdef(fn.numeral)?fn.numeral:'0,0.00';
				return numeral(cv).format(format)
			}
		}
	},
	checkbox: {
		tdclass:'col-ch',
		cv: function(fc,cv){ return '<p><i class="fa fa'+(cv==1?'-check':'')+'-square-o"></i></p>' }
	},
	roles: {
		cv: function(fc,cv){ return '<p>'+cv+' <button class="pnk-roles"><i class="fa fa-key"></i></button></p>' }
	},
	gallery: {
		cv: function(fc,cv){ return '<p>'+cv+' <button class="pnk-gallery"><i class="fa fa-camera"></i></button></p>' }
	},
	pills: {
		cv: function(fc,cv){
			dv='';
			$.each(fc.options, function(index,value) { if(index!=cv) dv+=' <button class="btn btn-default bg-option up-button" value="'+index+'">'+value+'</button>'; });
			return dv;
		}

	}
	// gallery roles
}
//legacy

PNK.set = function (el)
{
	if(typeof el=='object') {
    this.el = el;
  } else this.el = document.getElementById(el);
	this.src = this.el.getAttribute('pnk-src')
	return this;
}

function isdef(x) {
	if (typeof x==='undefined') return false;
	return true
}

function pnk_populate_tables(div) {
  // Add table inside every .pnk-table div
	tablei = 0;
	$('.pnk-table').each(function () {
		tablei++;
		PNK.set(this).create('pnk-t' + tablei);
	});
}

/********************************************/



$('body').on('click', '#submit_reload', function () {
  PNK.set($(this).closest('.pnk-table')).load()
});

///////////////////////////////////
//  pnk_generator()  will create the table and return the html, needs the name of the

PNK.create = function (tableid)
{
  console.log('Creating '+this.el.getAttribute('pnk-src'))
  var pnksrc = this.src;
  let div = this.el;

	if (typeof pnk_src[pnksrc] != 'undefined') {
		var data = pnk_src[pnksrc];
		PNK.create_table(tableid, data);
	} else {
		$.getJSON( "pnk/fields?t="+div.getAttribute('pnk-src'), function( data ) {
			pnk_src[pnksrc] = data;
			PNK.create_table(tableid, data);
		});
	}
}

PNK.create_table = function (tableid, data)
{
  var div=$(this.el)
	var pnksrc = div.attr('pnk-src');
	var gsr = '';

	// Dropdown of fields to group by the results
	if(isdef(data.grouping)){
			gsr+='<div>'+_e('Group by')+'<select class="pnk-groupby pnk-select"><option value=""></option>';
			$.each( data.grouping, function( key, value ) {
			gsr+='<option value="'+value+'">'+data.fields[value].title+'</option>';
		});
		gsr+='</select></div>';
	}

	// Search boxes
  if(isdef(data['search-box']))
    gsr=gsr+'<div col="search"><input type="text" class="pnk-input" value=""></div>';

	if(isdef(data['search-boxes'])) for(i=0;i<data['search-boxes'].length;i++){
		var fid=data['search-boxes'][i];
		var df=data.fields[fid];
		if(isdef(df.title)) ftitle=df.title; else ftitle=fid;
		gsr=gsr+'<div col="'+fid+'">'+ftitle+PNK.createFilter(df, ['','',''] )+'</div>';
		// We add the datalists in the end of the function
	}

	// Buttons //'+_e('Search')+'
	if(gsr!="") gsr='<div class="pnk-searchbox" style="display:inline-block">'+gsr+'<button class="btn btn-default com-search"><i class="fa fa-search"></i> </button></div>';

  // The toolbox
  var tls = pnk_table_head(div, tableid, data);

  if( div.attr('pnk-head-target') ) {
    $(div.attr('pnk-head-target')).html(tls);
  }else gsr="<div style='margin-bottom:6px;min-height:34px;display:inline-block; width:100%'>"+gsr+tls+"</div>";

	var tstyle='';

	var filters='',findex='',updf=[];
	div.find('.pnk-searchbox .pnk-input').each( function( index ) {
		if(!isdef($(this).attr('col'))) return;
		if(!isdef($(this).attr('colf'))) colf=$(this).attr('colf'); else colf='';
		if($(this).val() != ''){
			var findex=$(this).closest('div').attr('col')+colf;
			updf[findex]=$(this).val();
			filters=filters+'&'+findex+'='+$(this).val();
		}
	});

	div.find('table').attr('filters',filters);

  var groupby="";
  if(data.groupby) groupby=data.groupby[0];

	div.html( '<div class="pnk-table-list">'+gsr+'<div><table class=""'+tstyle+' id="'+tableid+'" pnk-src="'+pnksrc+'" cur-page="1" filters="&'+div.attr('filters')+'" group-by="'+groupby+'"><thead></thead><tbody></tbody><tfoot></tfoot></table></div></div><div class="pnk-edit" style="display:none"></div>');

	if(typeof data['search-boxes'] != 'undefined') for(i=0;i<data['search-boxes'].length;i++){
		var fid=data['search-boxes'][i];
		var df=data['fields'][fid];
		// Datalist
		if(df['type']=="datalist"){
			var in_dl=div.find('div[col='+fid+'] input');
			in_dl.attr('list',fid+'_sdl');

			var dl='';
			jQuery.each(df['datalist'],function(i,v) { dl=dl+'<option value="'+v+'">'; });
			in_dl.after('<datalist id="'+fid+'_sdl">'+dl+'</datalist>');
		}
	}

	PNK.load();

  //if(typeof $.chosen === "function") $(".pnk-select").chosen({disable_search_threshold: 10});
  //div.find('table').addClass("table table-hover table-striped table-bordered table-condensed");
}

// The title and toolbox
function pnk_table_head(div, tableid, data){
  var tls = '';

	if(data['tools']) for(i = 0; i < data['tools'].length; i++) {
		var tl = data['tools'][i];
		bc = ""; tb = ""; xtr = "";

    if(PNK.tools[tl]) {
      if(PNK.tools[tl].title) tb=_e(PNK.tools[tl].title); else tb="";
      if(PNK.tools[tl].extra) xtr=PNK.tools[tl].extra; else xtr="";
      if(PNK.tools[tl].after) aftr=PNK.tools[tl].after; else aftr="";
      if(PNK.tools[tl].fa) tfa='<i class="fa fa-'+PNK.tools[tl].fa+'"></i>'; else tfa="";
      tls+='<button type="button" class="btn btn-default" tool="'+tl+'" '+bc+xtr+'>'+tfa+" "+tb+aftr+'</button>';
    }
	}

  return "<div class='pnk-tools btn-group pull-right' pnk-src='"+data['name']+"' table-id='"+tableid+"'>"+tls+"</div>";
}


PNK.load = function ()
{
	var id=$(this.el).find('table').attr('id')
	var table=$(this.el).find('table').get(0)
	var thead=$('#'+id+' thead');
	var tbody=$('#'+id+' tbody');
	var tfoot=$('#'+id+' tfoot');
	var pnksrc=$(table).attr('pnk-src');
	var orderby=$(table).attr('order-by');
	if(typeof orderby=='undefined') porderby=''; else porderby='&orderby='+orderby;
	if(typeof groupby=='undefined') pgroupby=''; else pgroupby='&groupby='+groupby;
	var groupfor=$('#'+id).attr('group-for');
	if(typeof groupfor=='undefined') pgroupfor=''; else pgroupfor='&groupfor='+groupfor;
	var page=$('#'+id).attr('cur-page');
	var filters=$('#'+id).attr('filters');
	var com_cols=0;
	// Get the rows from the source file
	$.getJSON( PNK.path + "list?t="+this.src+"&page="+page+porderby+pgroupby+pgroupfor+filters, function( data ) {
		// Put the titles of columns
		thead.html( PNK.thead(id,data) );
		PNK.colspan=0;
		$('#'+id).find(" thead tr:first th").each( function(){
				if($(this).hasClass("col-hide")==false) PNK.colspan++;
		});

		// Put the rows
		if(isdef(data['rows'])){
			tbody.html( PNK.load_rows(pnksrc, data) );
			if(!$(table).attr('group-by')) classtr='main-row'; else classtr='group-row';
			tbody.children('tr').addClass(classtr);
            if(isdef($.tooltip)) $('.pnk-com').tooltip();
		}else tbody.html('<tr><td colspan="'+this.colspan+'">No rows here</td></tr>');

		tfoot.html( PNK.tfoot(data) )

		//$( "#"+id+" thead tr th" ).resizable();
		//$( "#"+id+" tbody" ).sortable();

    if (typeof pnk_end_load_table === 'function') pnk_end_load_table();
    if(isdef(PNK.load_fn)) PNK.load_fn();
	});
}


PNK.reload = function (attr,value)
{
	$(this.el).find('table').attr(attr, value );
	PNK.load();
}

PNK.load_rows = function (pnksrc, data,args={})
{
	var gtr="",ctd="";
	var pnkt=pnk_src[pnksrc];
	var pnk_fields=pnkt.fields;
	var rows=data['rows'];

	if(!isdef(args.group_level)) group_level = 0; else group_level = args.group_level+1;

	if(isdef(rows)) for(var i = 0, len = rows.length; i < len; i++) {
		var no_edit=0;
		var gtd="",row_id="";
		var rv=Array;

		for(var j = 0, lenj = rows[i].length; j < lenj; j++) rv[ data['fields'][j] ]=rows[i][j];

    	// output the cells for every row
    	for(var j = 0, lenj = rows[i].length; j < lenj; j++) {
					// print the cell
					var col=data.fields[j];
					var field=pnk_fields[col]

      		display_cv=PNK.cv(rv[col], field, rv, []);

					tdclass=''
      		if(field['class']) tdclass+=field['class'];
					if(field['show']==false) tdclass+=' col-hide';
					if(isdef(PNK.field_types[field.type]))
							tdclass+=' '+PNK.field_types[field.type].tdclass;

      		if(pnkt['groupby']) {
        		if(pnkt['groupby'][group_level] == col) if(display_cv != "")  tdclass +=' col-group';
        		for(g=0; g<group_level; g++) if(pnkt['groupby'][g] == col) display_cv="";
      		}

			if(pnkt['edit']) if(pnkt['edit']==true) if(field['edit']!=false) if(col!=pnkt['id']) tdclass+=' pnk-editable';

      		if(field['rowspan']!=true)
        		gtd = gtd + '<td col="' + col + '" value="' + rows[i][j] + '" class="' + tdclass + '">' + display_cv + '</td>';
      		else if(i == 0) gtd = gtd+'<td rowspan="1000" col="'+col+'" value="'+rows[i][j]+'" class="'+tdclass+'">'+display_cv+'</td>';

			if( pnkt['id']==col ) row_id=rv[col];
			// check if the is a no-edit filter
			if(typeof pnkt['no-edit']!= 'undefined') if(pnkt['no-edit'][0]==col) if(pnkt['no-edit'][1]==rows[i][j]) no_edit=1;
		}

    	// Add commands
    	ctd='';
    	if(isdef(pnkt.commands)){
      		ctd=ctd+'<td>';
      		if(isdef(row_id)) if(row_id!="") $.each( pnkt.commands, function( key, key_value ) {
        		if(no_edit==1) if((key=='edit')||(key=='delete')) return;

        		if(typeof PNK.commands[key_value] != 'undefined') {
          			var fa = PNK.commands[key_value].fa;
          			var title = PNK.commands[key_value].title;
          			ctd=ctd+'<i class="fa fa-'+fa+' pnk-com com-'+key_value+'" title="'+title+'"></i>';
        		}
      		});
			ctd=ctd+'</td>';
    	}

    	// Add checkbox
    	if(pnkt.bulk_actions) chtd = '<td><i class="fa fa-square-o tr_checkbox"></td>'; else chtd='';

    	if(row_id != "") row_id='row-id="'+row_id+'"';
    	var row_filters="", row_group_level="", row_class="";
    	if(args.filters) row_filters=' filters="'+args.filters+'"';
    	if(args.trclass) row_class=' class="'+args.trclass+'"';
    	if(group_level) row_group_level=' group_level="'+group_level+'"';

        newtr = '<tr '+row_id+row_class+row_group_level+row_filters+'>'+chtd+gtd+ctd+'</tr>';
        //if(typeof PNK.tr_fn !== 'undefined') PNK.tr_fn(newtr);
		gtr=gtr+newtr;
	}

	return gtr;
}

PNK.thead = function (id,data)
{
	var table=$(this.el).find('table').get(0)
	var pnk_srcid=pnk_src[this.src];
	var orderby=$(table).attr('order-by');
	var gth="";

  if(isdef(pnk_srcid['bulk_actions'])){ // Add checkbox
		gth=gth+'<th style="width:28px;"><i class="fa fa-square-o bulk_checkbox" aria-hidden="true"></i></th>';
	}

	for(let index in data.fields) if (typeof data.fields[index] !== 'function') {
    var field = data.fields[index];
		var df=pnk_srcid.fields[field];
    var sprht='';var grbut='';var thclass="";var thstyle="";

		if(isdef(df.title)) title = df.title; else title = field;

		thclass="sorting";
		if(df.thclass) thclass += " "+df['thclass'];

		if(isdef(df.show)) if(df.show==false) thclass+=' col-hide';
		if(df.type) if(df.type=="number") thclass += ' col-number';

    if(isdef(df.style)) thstyle=' style="'+df.style+'"';

    sortic='fa fa-sort';
    if( orderby == field+'_a') sortic = 'sort-asc fa fa-sort-down';
    if( orderby == field+'_d') sortic = 'sort-desc fa fa-sort-up';
    sorti='<i class="pnk-icon pnk-sorti '+sortic+'" col="'+field+'"></i>';

		gth=gth+'<th col="'+field+'"'+thstyle+' class="'+thclass+'">'+sorti+title+'</th>';
	}

	if(isdef(pnk_srcid['commands'])) {
		gth=gth+'<th style="width:'+(pnk_srcid['commands'].length*28)+'px"></th>'; // Leave a column for the commands
	}

	return '<tr>'+gth+'</tr>';
}

function pnk_load_srow(id,data)
{
	var pnk_srcid=pnk_src[ $('#'+id).attr('pnk-src') ];
	var gsr="";
	var tsr=""
	for(var ind in data['fields']) {
		var field=data['fields'][ind];
		var df=pnk_srcid['fields'][field];
		var v=['','',''];
		var dff=data['filtered'][field];
		if(isdef(data['filtered'])) {
			if(isdef(dff)){
				if(Array.isArray(data['filtered'][field])){
					if(isdef(dff[0])) v[1] = dff[0];
					if(isdef(dff[1])) v[2] = dff[1];
				}else v[0]=dff;
			}
		}
		gsr=gsr+'<td col="'+field+'" style="max-width:'+$('#'+id+' thead th[col='+field+']').width()+'px">'+PNK.createFilter(df,v)+'</td>';
	}


	if(isdef(pnk_srcid['commands'])) {
		gsr=gsr+'<td><i class="fa fa-search pnk-com com-search"></i><i class="fa fa-refresh pnk-com com-refresh"></i></td>';
	}
	return '<tr class="pnk-srow">'+gsr+'</tr>';
}

// print the cell
PNK.cv = function (cv,fc,rv)
{
	if($.isArray(cv)) dv=''; else dv=cv;
	if(cv!='') {

		//////// Check the type
		if(isdef(fc.type)) if(isdef(PNK.field_types[fc.type]))
				if(isdef(PNK.field_types[fc.type].cv)) return PNK.field_types[fc.type].cv(fc,cv)

		/////// Option list
		if(isdef(fc.options)) if(isdef(fc.options[cv])) {
			dv=fc.options[cv];
			if(typeof fc['png_url'] != "undefined" ) dv='<img src="'+fc['png_url']+cv+'.png">';
			if(typeof fc['bg-options'] != "undefined" ) dv='<div style="padding:5px;color:white;background:'+fc['bg-options'][cv]+'" class="bg-option">'+dv+'</div>';
		}

		/////// Date format
		if(isdef(fc.dateFormat)){
			dv= $.datepicker.formatDate( fc.dateFormat, new Date( cv.replace(/-/g, '/') ) );
		}

		}
		/////// Eval
		if(isdef(fc.eval)) eval(fc.eval);
  	/////// Parcial
		if(isdef(fc.partial)) dv=dv+' <i class="fa fa-chevron-down partial-dr" partial-src="'+fc.partial[0]+'" partial-id="'+fc.partial[1]+'"></i>';

		return dv;
}

PNK.tfoot = function (data)
{
	var pagination=pnk_src[this.src]['pagination'];

	if(isdef(pagination)) if($('table',this.el).attr('group-by')==''){
		var curPage=parseInt((data.startIndex+1)/pagination)+1;
		var totalPage=parseInt((data.totalRows-1)/pagination)+1;
		return '<tr><td colspan="'+this.colspan+'"><ul class="pagination">'+ PNK.pagination(parseInt(curPage), parseInt(totalPage)) +'</ul></td></tr>'
	}else return '<tr><td colspan="'+this.colspan+'"></td></tr>'
}

//////////// Pagination
////////////
PNK.pagination = function (curPage, totalPage)
{
	var gtr='';
	var firstPage=curPage-4;
	var lastPage=curPage+3;
	if(firstPage<2) firstPage=1; else gtr+='<li><a href="#">1</a></li>';//'<button class="btn btn-default">1</button>...';
	if(lastPage<=firstPage+8) lastPage=firstPage+8;
	if(lastPage>totalPage) lastPage=totalPage;

	for(var i=firstPage; i<lastPage+1; i++){
    if(i==curPage) gtr+='<li class="active"><a href="#">'+i+'</a></li>'; else gtr+='<li><a href="#">'+i+'</a></li>';
	}
	if(lastPage<totalPage) gtr+='<li><a href="#">'+totalPage+'</a></li>';

	return gtr;
}

$(document).on('click','.pnk-table table tfoot tr td ul a',function(event){
  event.preventDefault();
	PNK.reload('cur-page', $(this).html() );
});

//////////// Th click
$(document).on('click','table thead tr th',function(){
	PNK.set($(this).closest('.pnk-table').get(0))
	var tid=$(this).closest('table').attr('id');
	var col=$(this).attr('col');
	if( $(this).hasClass('sorting') ){
		if( $(this).closest('table').attr('order-by')==col+'_d' )
			PNK.reload('order-by',col+'_a'); else PNK.reload('order-by',col+'_d');
	}
	if( $(this).hasClass('grouping') ){
		if( $(this).closest('table').attr('group-by')==col ) PNK.reload('group-by',''); else PNK.reload('group-by',col);
	}
});


$(document).on('click','.pnk-table table tr td p .pnk-gallery',function()
{
	// Gallery Dialog
	var id=$(this).closest('tr').attr('row-id');
	var f=$(this).closest('td').attr('col');
	var t=$(this).closest('.pnk-table').attr('pnk-src');
	var df=pnk_src[t]['fields'][f];

	var newDiv = $(document.createElement('div'));
	$(newDiv).html('<iframe id="fileframe" src=PNK.path + "pnk-gallery.php?t='+t+'&f='+f+'&id='+id+'" style="width:800px;height:66%;">');
	$(newDiv).attr('title',df['title']).dialog({dialogClass: "pnk-dialog w60" }); //
});



//////////// Td click
///////////

$(document).on('click','.pnk-table table tbody .main-row td',function(){
	var pid=$(this).closest('.pnk-table').attr('pnk-src');
	if(isdef(pnk_src[pid].td_command)) $(this).closest('.pnk-table').find('.com-'+pnk_src[pid].td_command).click();

	// Turn cell into input
	/*if(pnk_src[pid]['edit-cells']){
		remove_inputs();
		if($(this).children('.pnk-input').length==0) cellInput(this);
	}*/
});


/////////////// Input field
///////////////
$(document).on('change','.pnk-table .pnk-input',function(){
	$(this).parent().addClass('to-save');
	$(this).closest('.pnk-table').children('.pnk-save').removeAttr('disabled');
});

/////////////// Group by field select
///////////////
$(document).on('change','.pnk-table .pnk-groupby',function(){
	var pnksrc=$(this).closest('.pnk-table').attr('pnk-src');
	var table=$(this).closest('.pnk-table').find('table[pnk-src='+pnksrc+']');
	var groupfor=$(this).closest('.pnk-table').find('.pnk-groupfor');
	var id=table.attr('id');
	table.attr('cur-page','1');
	var val='';
	if(isdef($(this).val())) val=$(this).val();
	if(isdef(groupfor)) if($(this).val()=='')  groupfor.css('visibility','hidden'); else groupfor.css('visibility','visible');
	PNK.reload('group-by', val );
});


$(document).on('change','.pnk-table .pnk-groupfor',function(){
	var pnksrc=$(this).closest('.pnk-table').attr('pnk-src');
	var table=$(this).closest('.pnk-table').find('table[pnk-src='+pnksrc+']');
	var id=table.attr('id');
	table.attr('cur-page','1');
	var val='';
	if(isdef($(this).val())) val=$(this).val();
	PNK.reload('group-for', val );
});



// Translation
function _e(phrase)
{
	if(isdef(lang_array)) if(isdef(lang_array[phrase])) return lang_array[phrase];
	return phrase;
}




$(document).on('click','.pnk-table .partial-dr',function()
{
	$('.detail-row').remove();
	$('.parent-row .partial-dr-x').switchClass('partial-dr-x fa-chevron-up','partial-dr fa-chevron-down',0);
	$('.parent-row').removeClass('parent-row');
	$(this).switchClass('partial-dr fa-chevron-down','partial-dr-x fa-chevron-up',0);
	//$(this).addClass('partial-dr-x fa-chevron-up');
	var tr=$(this).closest('tr');
	var filters=$(this).attr('partial-id')+'='+tr.attr('row-id');

	tr.addClass('parent-row');
	tr.after('<tr class="detail-row"><td colspan="'+tr.children().length+'"><div class="pnk-table" pnk-src="'+$(this).attr('partial-src')+'" filters="'+filters+'"></div></td></tr>');
	var ptableid=$(this).closest('table').attr('id');
	var dr_pnk=$('.detail-row td .pnk-table');
	PNK.set(dr_pnk).create(ptableid+'-partial');
});
$(document).on('click','.pnk-table .partial-dr-x',function()
{
	$('.detail-row').remove();
	$('.parent-row').removeClass('parent-row');
	$(this).switchClass('partial-dr-x fa-chevron-up','partial-dr fa-chevron-down',0);
});


$(document).on('click','.pnk-table tr .col-ch p',function()
{
	var tr= $(this).closest('tr');
	var cell=$(this).closest('td');
	var t=$(this).closest('.pnk-table').attr('pnk-src');
	//var tid=$(this).closest('table').attr('id');
	var col=cell.attr('col');
	var v=cell.attr('value');
	if(v=='0') v=1; else v=0;
	$.post(
		PNK.path + "uc?t="+t,
		{ 'col' : col, 'v' : v, 'rid' : tr.attr('row-id') },
		 function(data) {
            xx = PNK.load_rows( t,data );
			tr.replaceWith( xx );
			tr.attr("class","main-row");
            if (PNK.load_fn !== 'undefined') PNK.load_fn();
            $('.pnk-com').tooltip();
		}
	,'json');
});
$(document).on('click','.pnk-table .up-button',function()
{
	var tr= $(this).closest('tr');
	var cell=$(this).closest('td');
	var t=$(this).closest('.pnk-table').attr('pnk-src');
	var col=cell.attr('col');
	var v=$(this).attr('value');
	$.ajax({
		url: PNK.path + "uc?t="+t,
		data: { 'col' : col, 'v' : v, 'rid' : tr.attr('row-id') },
		dataType: "json",
		type: 'post',
		success: function(data) {
			tr.replaceWith( PNK.load_rows( t, data ) );
      $('.pnk-com').tooltip();
			tr.attr("class","main-row");
      if (typeof pnk_end_up_button === 'function') pnk_end_up_button();
		}
	});
});


/////////// Methods


//Function to convert hex format to a rgb color
function rgb2hex(rgb)
{
 rgb = rgb.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);
 return (rgb && rgb.length === 4) ? "#" +
  ("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
  ("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
  ("0" + parseInt(rgb[3],10).toString(16)).slice(-2) : '';
}
function pad (str, max) {
  str = str.toString();
  return str.length < max ? pad("0" + str, max) : str;
}


function pnk_delete_row(tr) {
  var id = tr.attr('row-id');
  var t = tr.closest('.pnk-table').attr('pnk-src');
  $.ajax({
		url: PNK.path + "delete?t="+t,
		data: { 'id' : id },
		type: 'post',
		success: function(data) {
			var trtr=$('.pnk-table[pnk-src="'+t+'"] tr[row-id="'+id+'"]');
			trtr.animate({backgroundColor: "#ff0000"}, 200, function(){ $(this).remove() } );
			//trtr.remove();
		}
	});
}


$(document).on('click','.pnk-table .com-list',function(){
	var t=$(this).closest('table');
	var tsrc=t.attr('pnk-src');
	var tlist= pnk_src[tsrc].commands.list;
	var colspan = t.find(" thead tr:first th").length;

	if(!isdef(tlist)) return;
	var tr=$(this).closest('tr');
	var gr_value=tr.find('td').first().attr('value');

	if(tr.hasClass('show-details')){
		tr.removeClass('show-details');
		tr.next().remove();
		return;
	}

	// Load the listed tables below
	var trd="";
	$.each(tlist,function(index,value){
		trd=trd+'<div class="pnk-table" pnk-src="'+value+'" ></div><br>';//.php?accident_id='+tr.attr('row_id')+'
	});
	tr.after('<tr class="detail-row"><td colspan="'+colspan+'">'+trd+'</td></tr>');
	$('.detail-row .pnk-table').each(function(index){
		PNK.set(this).create('pnk-l'+index);
	});
	//	$('tbody tr').not('.group-row').fadeOut(0);
	//	$('tbody tr').not('.group-row').fadeIn(200);
	tr.addClass('show-details');
});



function jsonEscape(str)  {
    return str.replace(/\n/g, "\\n").replace(/\r/g, "\\r").replace(/\t/g, "\\t");
}


function switch_div(parent,child){
	$(parent).children().css('display','none')
	$(parent).children(child).css('display','block')
}

function edit_dialog(pnk_table,action,id=""){
	var sopt='';
	var t=$(pnk_table).attr('pnk-src');
	var tid=$(pnk_table).attr('id');
	var editDiv = $(pnk_table).closest('.pnk-table').find('.pnk-edit');
	switch_div($(pnk_table).closest('.pnk-table'),'.pnk-edit');

  var row_id="";
	if(id==""){
		dbupt=_e('Save');
		//dbt=_e('New Registry');
	}else{
		dbupt=_e('Update');
		//dbt=_e('Edit Registry');
    row_id=id;
	}
  if(action=="clone"){
    dbupt=_e('Save');
		//dbt=_e('Clone Registry');
    row_id="";
  }

	btns='<div style="width: 100%;text-align:right;display: inline-block;margin-bottom:8px;border-top:1px solid #ccc"><br><br><button class="btn btn-primary pnk-edit-save" type="button" row-id="'+row_id+'" tid="'+tid+'">'+dbupt+'</button> <button class="btn btn-danger pnk-edit-cancel" type="button">'+_e('Cancel')+'</button></div>';

	// Add input for every field
	$.ajax({
		url: PNK.path + "edit?t="+t, data: { 'id' : id }, dataType: "json",type: 'post',
		success: function(data) {

			for(i=0; i<data.fields.length; i++) {
				field=pnk_src[t]['fields'][ data.fields[i] ];
                if(isdef(field['edit'])) if(field['edit'] == false) continue;
				if(isdef(field['title'])) title=field['title']; else title=data.fields[i];
				sopt=sopt+'<div class="update-div" col="'+data.fields[i]+'"><label for="'+data.fields[i]+'">'+title+'</label>'+PNK.createInput(field,data.rows[0][i])+'</div>';
			}

			//<span class="pnk-edit-title">'+dbt+'</span>
			editDiv.html('<div style="width: 100%;display: inline-block;margin-bottom:8px;border-bottom:1px solid #ccc"></div>'+sopt+btns);
			editDiv.attr('tid',tid);

            var textareas = document.getElementsByTagName('textarea');
            var count = textareas.length;
            for(var i=0;i<count;i++){
                textareas[i].onkeydown = function(e){
                    if(e.keyCode==9 || e.which==9){
                        e.preventDefault();
                        var s = this.selectionStart;
                        this.value = this.value.substring(0,this.selectionStart) + "\t" + this.value.substring(this.selectionEnd);
                        this.selectionEnd = s+1;
                    }
                }
            }

			for(i=0;i<data.fields.length;i++){
				field=pnk_src[t]['fields'][ data.fields[i] ];
				// Datalist
				if(field['type']=="datalist"){
					var in_dl=$(editDiv).find('div[col='+data.fields[i]+'] input');
					in_dl.attr('list',data.fields[i]+'_dl');
					var dl='';
					jQuery.each(field['datalist'],function(i,v) { dl=dl+'<option value="'+v+'">'; });
					editDiv.append('<datalist id="'+data.fields[i]+'_dl">'+dl+'</datalist>');
				}
				// Sort by text
				if(field['sort']) if(field['sort']=="text"){
					var sel=$(editDiv).find('#in-'+data.fields[i]+' select');
					var savedv=sel.val();
					sel.append(sel.find("option").remove().sort(function(a, b) {
						var at = $(a).text(), bt = $(b).text();
						return (at > bt)?1:((at < bt)?-1:0);
					}));
					sel.val(savedv).change();
				}
			}


            if(typeof $.fn.chosen != 'undefined') $(".pnk-select").chosen({disable_search_threshold: 10});
      		if(typeof $.fn.select2 != 'undefined') $(".pnk-select").select2();
	       if(typeof($.fn.datetimepicker) == "function") $(".datetimepicker").datetimepicker({format: 'Y-m-d H:i:s'});
	       if(typeof(Pikaday) == "function") {
					 allx = g('.datepicker').all
					 for (var i=0, max=allx.length; i < max; i++) {
						 new Pikaday({
							 field: allx[i],
							 format: 'YYYY-MM-DD',
						   toString(date, format) {
								 // you should do formatting based on the passed format,
								 // but we will just return 'D/M/YYYY' for simplicity
								 	const day = date.getDate();
									const month = date.getMonth() + 1;
									const year = date.getFullYear();
									return `${year}-${month}-${day}`;
								},
								parse(dateString, format) {
								// dateString is the result of `toString` method
								const parts = dateString.split('-');
								const day = parseInt(parts[2], 10);
								const month = parseInt(parts[1] - 1, 10);
								const year = parseInt(parts[0], 10);
								return new Date(year, month, day);
							}
					});
				}
		   }

		   if(typeof(CodeMirror) != "undefined")
			    pnk_cmirror=CodeMirror.fromTextArea(g('.codemirror-js').all[0],{lineNumbers:true,mode:'javascript'});
		}
	});
}

PNK.edit_save = function (btn)
{
	var id=btn.getAttribute('row-id');
	var t=this.src;
	var tid=btn.getAttribute('tid');
	var data='{"'+id+'":{';
	var dcv='';

	$(btn).closest('.pnk-edit').find( '.update-div' ).each( function() {
		if(dcv!='') dcv+=',';
		_input=$('.pnk-input',this);
		_col = '"'+$(this).attr('col')+'":';

		if(_input.prop("tagName")=='TEXTAREA') {
			if(_input.hasClass('codemirror-js') && (isdef(pnk_cmirror))) {
				/*try {
					JSON.parse(pnk_cmirror.getValue());
				} catch (e) {
					//alert('Wrong json format');
					return false;
				}*/
				dcv+=_col+JSON.stringify(pnk_cmirror.getValue());
				delete pnk_cmirror;
			} else dcv+=_col+'"'+_input.html()+'"';
		}else dcv+=_col+'"'+_input.val()+'"';
	});

	// Read the filters and add them in data
	filters=$(this.el).find('table').attr('filters');
	if(isdef(filters)) if(id==''){
		farray=filters.split("&");

		for (i = 0; i < farray.length; i++) if(isdef(pnk_src[PNK.src].fields[i])) {
			frow=farray[i].split('=');
			if(dcv!='') dcv+=',';
			dcv+='"'+frow[0]+'":"'+frow[1]+'"';
		}
	}
	data+=dcv+'}}';

	console.log(data)
	data =jsonEscape(data);

		$.ajax({
			url: PNK.path + "update?t="+t, data: { 'erows' : data }, dataType: "json", type: 'post',
			//cache:  false ,
			success: function(data) {
				if(id!=""){
					var tr=$('#'+tid+' tr[row-id="'+id+'"]');
					tr.replaceWith( PNK.load_rows( pnk_src[t], data ));
          $('.pnk-com').tooltip();
					var newtr=$('#'+tid+' tr[row-id="'+id+'"]');
					newtr.addClass('main-row');
					var tbg=newtr.css("background-color");
					newtr.css("background-color","#ffff00");
					newtr.animate({backgroundColor: rgb2hex(tbg)}, 300 );
				}else{
					$('#'+tid+' tbody').prepend( PNK.load_rows( t, data ) );
          $('.pnk-com').tooltip();
					var newtr=$('#'+tid+' tr[row-id="'+data['rows'][0][0]+'"]');
					newtr.addClass('main-row');
					var tbg=newtr.css("background-color");
					newtr.css("background-color","#00ff00");
					newtr.animate({backgroundColor:  rgb2hex(tbg) }, 200);
				}

        if (typeof pnk_end_edit_save === 'function') pnk_end_edit_save();
			}
		});

	switch_div( $(btn).closest('.pnk-table'),'.pnk-table-list');

}

$('body').on('click','.pnk-edit-save',function(e){
	PNK.set($(this).closest('.pnk-table').get(0)).edit_save(this)
});

$('body').on('click','.pnk-edit-cancel',function(e){
	switch_div( $(this).closest('.pnk-table'),'.pnk-table-list');
});

$('body').on('click','.ui-dialog-titlebar-close',function(e){
	dialog=$(this).closest('div.ui-dialog');
	dialog.remove();
});

$(document).on('click','.pnk-table .com-search',function()
{
	var pnk_id=$(this).closest('.pnk-table').find('table[pnk-src]').attr('id');
	var filters='',findex='';
	var updf=[];
	// First update the attribute 'filters'
	$(this).closest('.pnk-searchbox').find('.pnk-input').each( function( index ) {
		if(!isdef($(this).parent().attr('col'))) return;
		if(!isdef($(this).attr('colf'))) colf=$(this).attr('colf'); else colf='';
		if($(this).val() != ''){
			findex=$(this).closest('div').attr('col')+colf;
			updf[findex]=$(this).val();
			filters=filters+'&'+findex+'='+$(this).val();
		}
	});

	PNK.reload('filters',filters);

});

$(document).on('click','.pnk-table .com-refresh',function()
{
	$(this).closest('.pnk-searchbox').find('div[col] .pnk-input').each( function( index ) {
		$(this).val('').change();
	});
	var pnk_id=$(this).closest('.pnk-table').find('table[pnk-src]').attr('id');
	PNK.reload('filters','');
});

////  Method to return the field Input
PNK.createInput = function (df,v)
{
  let arrv = new Array();
	// Select Dropdown
	if(df['options']) if(df['type']!="roles"){
			var sopt='';
			opn = 0;
			if(v=='') sopt='<option value="">-</option>';
			if(df['type']=='joins') arrv=v.split(","); else arrv[0]=v;
			if(df['type']=='meta') arrv=v.split(","); else arrv[0]=v;

			for(var temp in df['options']){
					if(arrv.indexOf(temp)>-1) selected=' selected'; else selected='';
					sopt+='<option value="'+temp+'"'+selected+'>'+df['options'][temp]+'</option>';
					opn++;
			}

			if( opn >5 ) d_l_s = ' data-live-search="true"'; else d_l_s = '';

    	if(df['type']=='joins' || df['type']=='meta') return '<select multiple class="pnk-input pnk-select" '+d_l_s+'>'+sopt+'</select>';
    	return '<select class="pnk-input pnk-select" '+d_l_s+'>'+sopt+'</select>';
	}

  	// Joins - Multiselect
/*  	if(df['type']=='joins' || df['type']=='meta'){
    	field_o = "<option value='1'>One</option><option value='2'>Two</option><option value='3'>three</option>";
    	return '<select multiple>'+field_o+'</select>';
  	}*/

  	// Field Types
	switch(df.type) {
		case 'checkbox': return '<input type="checkbox" class="pnk-input" value="'+(v==1?'1':'0')+'" '+(v==1?'checked':'')+'>';
		case 'text': return '<textarea class="pnk-input codemirror-js">'+v+'</textarea>';
		case 'date': return '<input class="pnk-input datepicker" value="'+v+'">';
		case 'password': return '<input class="pnk-input" type="password" value="'+v+'">';
		case 'datetime': return '<input class="pnk-input datetimepicker" value="'+v+'">';
	}

	return '<input type="text" class="pnk-input" value="'+v+'">';
}

////  Method to return the field Filter
PNK.createSelect = function (options,v,ho='')
{
	var sopt=ho;
	for(var temp in options){
		if(v==temp) selected=' selected'; else selected='';
		sopt+='<option value="'+temp+'"'+selected+'>'+options[temp]+'</option>';
	}
	return '<select class="pnk-input pnk-select">'+sopt+'</select>';
}
PNK.createFilter = function (df,v){
	// Select Dropdown
	if(df.options) if(df.type!="roles") return PNK.createSelect(df.options,v[0],'<option value="">-</option>')
	if(df['search-options']) return PNK.createSelect(df['search-options'],v[0])

	var typec="";

	if(df['type']=="checkbox"){
		if(v[0]=="1") sel1=" selected>"; else sel1=">";
		if(v[0]=="0") sel0=" selected>"; else sel0=">";
		return '<select class="pnk-input"><option value="">-</option><option value="1"'+sel1+_e('Yes')+'</option><option value="0"'+sel0+_e('No')+'</option></select>';
	}

	if(df['type']=="date") typec='datepicker'

	if(df.searchbox=="range" || df.searchbox=="period"){
		return '<input colf="_from" class="pnk-input '+typec+'" placeholder="'+_e('from')+'" value="'+v[1]+'">-<input colf="_to" class="pnk-input '+typec+'" placeholder="'+_e('to')+'" value="'+v[2]+'">';
	}

	return '<input type="text" class="pnk-input '+typec+'" value="'+v[0]+'">';
}


/***********  C O M M A N D S  ****************/

$(document).on('click','.pnk-com',function(){
  var e = new Array();
  var command = $(this).attr("com");
  for(i in PNK.commands) if($(this).hasClass("com-"+i)) command=i;
  e.src = $(this).closest('table').attr('pnk-src');
	e.table = $(this).closest('table');
  e.row_id = $(this).closest('tr').attr('row-id');
  e.row = $(this).closest('tr');
  e.tr = e.row;
  PNK.commands[command].fn(e);
});

PNK.commands = {
  edit: { fa: "pencil", title: "Edit", fn: function(e){ edit_dialog(e.table,"edit",e.row_id); }},
  clone: { fa: "copy", title: "Clone", fn: function(e){ edit_dialog(e.table,"clone",e.row_id); }},
  pdf: { fa: "file", title: "Pdf", fn: function(e){ window.open(PNK.path + "pdf.php?t="+e.src+"&id="+e.row_id); }},
  delete: { fa: "trash-o", title: "Delete", fn: function(e){
  	if ( confirm(_e("Delete registry?"))) pnk_delete_row(e.tr)
  }}
}


/***********  T O O L B O X  ****************/

$(document).on('click','.pnk-tools button[tool]',function() {
  var e = new Array();
  var tool = $(this).attr("tool");
	e.pnk_table = $(this).closest('.pnk-table');
	e.table = $(this).closest('.pnk-table').find('table');
  e.tr_list = e.pnk_table.find('tr.selected');
  e.tr_array = new Array();
  e.tr_list.each(function(){ e.tr_array.push( $(this).attr('row-id') ); });
  e.tr_ids = e.tr_array.toString();
  PNK.tools[tool].fn(e);
});

PNK.tools = {
  add: { fa: "plus", title: "New", fn: function(e){ edit_dialog(e.table,"new"); }},
  pdf: { fa: "file-text", title: "Pdf", fn: function(e){ PNK.window_open(e,"pdf"); }},
  csv: { fa: "download", title: "Csv", fn: function(e){ PNK.window_open(e,"csv"); }},
  xls: { fa: "download", title: "Xls", fn: function(e){ PNK.window_open(e,"xls"); }}
}

PNK.tools.delete = { fa: "trash", title: "Delete", fn: function(e){
  if(e.tr_list.length>0) {
    if ( confirm(_e("Delete selected registries?")) == false) return;
    e.tr_list.each(function(){
      pnk_delete_row($(this));
    });
}}};
PNK.tools.search = { fa: "search", title: "Search", fn: function(e){
  e.pnk_table.find('.pnk-srow').toggle();
  e.pnk_table.find('.pnk-searchbox').toggle();
}};
PNK.tools.clipboard = { fa: "copy", title: "Clipboard", fn: function(e){
  var xls='<textarea style="width:100%">'+e.pnk_table.find(".select-result").text()+'</textarea>';
	$(xls).attr('title',_e("Select Data")).dialog(  );//{dialogClass: "pnk-dialog"}
}};



PNK.window_open = function (e,file) {
  var id = e.table_id;//$(this).closest('.pnk-tools').attr('table-id');
	var t = $('#'+id).attr('pnk-src');
	var orderby=$('#'+id).attr('order-by');
	var groupby=$('#'+id).attr('group-by');
	var filters=$('#'+id).attr('filters');

	window.open(PNK.path + "pnkfile?t="+t+"&file="+file+"&orderby="+orderby+"&groupby="+groupby+filters);
}



$(document).on("click",".col-group",function(){
  var pf = $(this).parent().attr("filters");
  var group_level = $(this).parent().attr("group_level");
  if(!isdef(group_level)) group_level = 0;
  var id = $(this).closest("table").attr("id");
  var t = $(this).closest("table").attr("pnk-src");
  if(!pf) pf="";
  var filters = pf + "&" + $(this).attr("col") + "=" + $(this).attr("value");
  var _this = $(this);
  var groupby = "";

  if(group_level == '') group_level = 0;

  // Get the rows from the source file
	$.getJSON( PNK.path + "list?t="+t+groupby+"&page=1"+filters, function( data ) {
    var new_rows = PNK.load_rows(pnk_src[t], data, { filters:filters, group_level:(group_level++) });
    _this.parent().after(new_rows);
    $('.pnk-com').tooltip();
  });

  $(this).removeClass("col-group");
  $(this).addClass("col-group-open");
});

$(document).on("click",".col-group-open",function(){
  var pf = $(this).parent().attr("filters");
  var t = $(this).closest("table").attr("pnk-src");
  if(!pf) pf="";
  var filters = pf + "&" + $(this).attr("col") + "=" + $(this).attr("value");
  var parent_id = $(this).parent().attr("row-id");
  $(this).closest('tbody').find( "tr[filters^='" + filters + "']" ).remove();
  $(this).removeClass("col-group-open");
  $(this).addClass("col-group");
});


$(document).on("click",".tr_checkbox",function(){
  if(!$(this).hasClass('checked')){
    $(this).closest('tr').addClass("selected");
    $(this).addClass("checked fa-check-square-o").removeClass("fa-square-o");
  }else{
    $(this).closest('tr').removeClass("selected");
    $(this).removeClass("checked fa-check-square-o").addClass("fa-square-o");
  }
  $(this).closest('table').find(".bulk_checkbox").addClass("fa-minus-square-o checked");
})

$(document).on("click",".bulk_checkbox",function(){
	table = $(this).closest('table');
	tr = table.find('tr');
	tr_ch = table.find('.tr_checkbox');

  if(!$(this).hasClass('checked')){
    tr.addClass("selected");
    tr_ch.addClass("checked fa-check-square-o").removeClass("fa-square-o");
    $(this).addClass("checked fa-check-square-o").removeClass("fa-square-o");
  }else{
    tr.removeClass("selected");
    tr_ch.removeClass("checked fa-check-square-o").addClass("fa-square-o");

    $(this).removeClass("checked").removeClass("fa-check-square-o").removeClass("fa-minus-square-o").addClass("fa-square-o");
  }
})
