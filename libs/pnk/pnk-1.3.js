//var pnk_list=[];
//let pnk_src = []
//let tablei
//let toolBarTarget = ""
if (typeof pnk_src == "undefined") pnk_src = []
if (typeof pnkPath == "undefined") pnkPath = "";

$('body').on('focus', ".datepicker", function () {
    if(typeof $.datepicker == "function") $(this).datepicker({
		dateFormat: 'yy-mm-dd',
		defaultDate: "-1w",
		changeMonth: true,
		changeYear: true
	});
});


/*
$(document).ready(function () {
	pnk_populate_tables(this)
});*/

function pnk_populate_tables(div) {
	// Add table inside every .pnk-table div
	tablei = 0;
	$('.pnk-table').each(function () {
		tablei++;
		pnk_create($(this), 'pnk-t' + tablei);
	});

}

$('body').on('click', '#submit_reload', function () {
	pnk_load($(this).closest('.pnk-table').find('table').attr('id'));
});

///////////////////////////////////
//  pnk_generator()  will create the table and return the html, needs the name of the

function pnk_create(div, tableid) {
	var pnksrc = div.attr('pnk-src');
	if (typeof pnk_src[pnksrc] != 'undefined') {
		var data = pnk_src[pnksrc];
		pnk_create_table(div, tableid, data);
	} else {
		$.getJSON( "pnk/fields?t="+div.attr('pnk-src'), function( data ) {
			pnk_src[pnksrc] = data;
			pnk_create_table(div, tableid, data);
		});
	}
}

function pnk_create_table(div, tableid, data){
	var pnksrc = div.attr('pnk-src');
	var gsr = '';


	// Dropdown of fields to group by the results
	if(typeof data.grouping !== 'undefined'){
			gsr+='<div>'+_e('Group by')+'<select class="pnk-groupby pnk-select"><option value=""></option>';
			$.each( data.grouping, function( key, value ) {
			gsr+='<option value="'+value+'">'+data.fields[value].title+'</option>';
		});

		gsr+='</select></div>';

	}

	// Search boxes
  if(typeof data['search-box'] !== 'undefined')
    gsr=gsr+'<div col="search"><input type="text" class="pnk-input" value=""></div>';

	if(typeof data['search-boxes'] !== 'undefined') for(i=0;i<data['search-boxes'].length;i++){
		var fid=data['search-boxes'][i];
		var df=data['fields'][fid];
		if(typeof(df.title) !='undefined') ftitle=df.title; else ftitle=field;
		gsr=gsr+'<div col="'+fid+'">'+ftitle+createFilter(df, ['','',''] )+'</div>';
		// We add the datalists in the end of the function
	}

	// Buttons //'+_e('Search')+'
	if(gsr!="") gsr='<div class="pnk-searchbox" style="display:inline-block">'+gsr+'<button class="btn btn-default com-search"><i class="fa fa-search"></i> </button></div>';

  // The toolbox
  var tls = pnk_table_head(div, tableid, data);

  //console.log(div.attr('pnk-head-target'));
  if( div.attr('pnk-head-target') ) {
    $(div.attr('pnk-head-target')).html(tls);
    console.log(div.attr('pnk-head-target'));
    console.log(tls);
  }else gsr="<div style='margin-bottom:6px;min-height:34px;display:inline-block; width:100%'>"+gsr+tls+"</div>";

	var tstyle='';

	var filters='',findex='',updf=[];
	div.find('.pnk-searchbox .pnk-input').each( function( index ) {
		if(typeof $(this).attr('col')=='undefined') return;
		if(typeof $(this).attr('colf') != 'undefined') colf=$(this).attr('colf'); else colf='';
		if($(this).val() != ''){
			var findex=$(this).closest('div').attr('col')+colf;
			updf[findex]=$(this).val();
			filters=filters+'&'+findex+'='+$(this).val();
		}
	});
	div.find('table').attr('filters',filters);

  //+tls
  var groupby="";
  if(data.groupby) groupby=data.groupby[0];
//table table-stripped table-bordered table-hover
	div.html( '<div class="pnk-table-list">'+gsr+'<div><table class="table table-hover table-bordered table-condensed"'+tstyle+' id="'+tableid+'" pnk-src="'+pnksrc+'" cur-page="1" filters="&'+div.attr('filters')+'" group-by="'+groupby+'"><thead></thead><tbody></tbody><tfoot></tfoot></table></div></div><div class="pnk-edit" style="display:none"></div>');

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

	pnk_load(tableid);

  //if(typeof $.chosen === "function") $(".pnk-select").chosen({disable_search_threshold: 10});
  //div.find('table').addClass("table table-hover table-striped table-bordered table-condensed");

}

// The title and toolbox
function pnk_table_head(div, tableid, data){
  //var t = '<span class="pnk-table-title">' + data["title"] + '</span>';
  var tls = '';

	if(data['tools']) for(i = 0; i < data['tools'].length; i++) {
		var tl = data['tools'][i];
		bc = ""; tb = ""; xtr = "";

    if(Pnk.tools[tl]) {
      if(Pnk.tools[tl].title) tb=_e(Pnk.tools[tl].title); else tb="";
      if(Pnk.tools[tl].extra) xtr=Pnk.tools[tl].extra; else xtr="";
      if(Pnk.tools[tl].after) aftr=Pnk.tools[tl].after; else aftr="";
      if(Pnk.tools[tl].fa) tfa='<i class="fa fa-'+Pnk.tools[tl].fa+'"></i>'; else tfa="";
      tls+='<button type="button" class="btn btn-default" tool="'+tl+'" '+bc+xtr+'>'+tfa+" "+tb+aftr+'</button>';
    }
	}

  return "<div class='pnk-tools btn-group pull-right' pnk-src='"+data['name']+"' table-id='"+tableid+"'>"+tls+"</div>";
}


function pnk_load(id)
{
	var div=$('#'+id);
	var thead=$('#'+id+' thead');
	var tbody=$('#'+id+' tbody');
	var tfoot=$('#'+id+' tfoot');
	var pnk_srcid=pnk_src[ $('#'+id).attr('pnk-src') ];
	var orderby=$('#'+id).attr('order-by');
	if(typeof orderby=='undefined') porderby=''; else porderby='&orderby='+orderby;
	var groupby=$('#'+id).attr('group-by');
	if(typeof groupby=='undefined') pgroupby=''; else pgroupby='&groupby='+groupby;
	var groupfor=$('#'+id).attr('group-for');
	if(typeof groupfor=='undefined') pgroupfor=''; else pgroupfor='&groupfor='+groupfor;
	var page=$('#'+id).attr('cur-page');
	var filters=$('#'+id).attr('filters');
	var com_cols=0;
	// Get the rows from the source file
	$.getJSON( pnkPath + "pnk/list?t="+div.attr('pnk-src')+"&page="+page+porderby+pgroupby+pgroupfor+filters, function( data ) {
		// Put the titles of columns
		thead.html( pnk_load_thead(id,data) );

        var colspan=0;
        $('#'+id).find(" thead tr:first th").each( function(){
            if($(this).hasClass("col-hide")==false) colspan++;
    });
		//if(typeof pnk_srcid.commands!='undefined') colspan=colspan+1; // Last column for the commands

		// Put the rows
		if(typeof data['rows']!=='undefined'){
			tbody.html( pnk_load_rows(pnk_srcid, data) );
			if(!div.attr('group-by')) classtr='main-row'; else classtr='group-row';
			tbody.children('tr').addClass(classtr);
      $('.pnk-com').tooltip();
		}else tbody.html('<tr><td colspan="'+colspan+'">No rows here</td></tr>');

		// Search row

		// Pagination
		if(typeof pnk_srcid['pagination']!== "undefined") if(groupby==''){
			var curPage=parseInt((data.startIndex+1)/pnk_srcid['pagination'])+1;
			var totalPage=parseInt((data.totalRows-1)/pnk_srcid['pagination'])+1;
			tfoot.html('<tr><td colspan="'+colspan+'"><ul class="pagination">'+ pagination(parseInt(curPage), parseInt(totalPage)) +'</ul></td></tr>');
		}else tfoot.html('<tr><td colspan="'+colspan+'"></td></tr>');


		//$( "#"+id+" thead tr th" ).resizable();
		//$( "#"+id+" tbody" ).sortable();

    if (typeof pnk_end_load_table === 'function') pnk_end_load_table();
    if(typeof Pnk.load_fn !== 'undefined') Pnk.load_fn();
	});


}


function pnk_reload(id,attr,value){
	$('#'+id).attr(attr, value );
	pnk_load(id);
}

function pnk_load_rows(pnkt, data,args={})
{

	var gtr="",ctd="";
	//var pnkt=pnk_src[ $('#'+id).attr('pnk-src') ];
	var pnk_fields=pnkt['fields'];
	var rows=data['rows'];

  if(typeof args.group_level == 'undefined') group_level = 0; else group_level = args.group_level+1;


	if(typeof rows!='undefined') for(var i = 0, len = rows.length; i < len; i++) {
		var no_edit=0;
		// print the row
		var gtd="",row_id="";
		var rv=Array;

    // set rv[] values for calculations
		for(var j = 0, lenj = rows[i].length; j < lenj; j++) rv[ data['fields'][j] ]=rows[i][j];

    // output the cells for every row
    for(var j = 0, lenj = rows[i].length; j < lenj; j++) {
			// print the cell
			var col=data['fields'][j];
			//var cv=rv[col];

      display_cv=pnk_cv(rv[col], pnk_fields[col], rv, []);

			tdclass=''
      if(pnk_fields[col]['class']) tdclass+=pnk_fields[col]['class'];
      if(pnk_fields[col]['type']=='number') tdclass+=' col-number';
			if(pnk_fields[col]['type']=='gallery') tdclass+=' col-number';
			if(pnk_fields[col]['type']=='roles') tdclass+=' col-roles';
			if(pnk_fields[col]['type']=='checkbox') tdclass+=' col-ch';
			if(pnk_fields[col]['show']==false) tdclass+=' col-hide';

      if(pnkt['groupby']) {
        if(pnkt['groupby'][group_level] == col) if(display_cv != "")  tdclass +=' col-group';
        for(g=0; g<group_level; g++) if(pnkt['groupby'][g] == col) display_cv="";
      }

			if(pnkt['edit']) if(pnkt['edit']==true) if(pnk_fields[col]['edit']!=false) if(col!=pnkt['id']) tdclass+=' pnk-editable';

      //display_cv = "<p>"+display_cv+"</p>";
      //display_cv = display_cv;

      if(pnk_fields[col]['rowspan']!=true)
        gtd = gtd + '<td col="' + col + '" value="' + rows[i][j] + '" class="' + tdclass + '">' + display_cv + '</td>';
      else if(i == 0) gtd = gtd+'<td rowspan="1000" col="'+col+'" value="'+rows[i][j]+'" class="'+tdclass+'">'+display_cv+'</td>';

			if( pnkt['id']==col ) row_id=rv[col];
			// check if the is a no-edit filter
			if(typeof pnkt['no-edit']!= 'undefined') if(pnkt['no-edit'][0]==col) if(pnkt['no-edit'][1]==rows[i][j]) no_edit=1;
		}

    // Add commands
    ctd='';
    if(typeof pnkt['commands']!="undefined"){
      ctd=ctd+'<td>';
      if(typeof row_id!="undefined") if(row_id!="") $.each( pnkt['commands'], function( key, key_value ) {
        if(no_edit==1) if((key=='edit')||(key=='delete')) return;

        if(typeof Pnk.commands[key_value] != 'undefined') {
          console.log("ok");
          var fa = Pnk.commands[key_value].fa;
          var title = Pnk.commands[key_value].title;
          ctd=ctd+'<span><i class="fa fa-'+fa+' pnk-com com-'+key_value+'" title="'+title+'"></i><span>';
        }
      });
      ctd=ctd+'</td>';
    }


    // Add checkbox
		//if(pnkt['bulk_actions']) chtd = '<td><input type="checkbox" /></td>'; else chtd = '';
    if(pnkt['bulk_actions']) chtd = '<td><i class="fa fa-square-o tr_checkbox"></td>'; else chtd='';

    if(row_id != "") row_id='row-id="'+row_id+'"';
    var row_filters="", row_group_level="", row_class="";
    if(args.filters) row_filters=' filters="'+args.filters+'"';
    if(args.trclass) row_class=' class="'+args.trclass+'"';
    if(group_level) row_group_level=' group_level="'+group_level+'"';
        newtr = '<tr '+row_id+row_class+row_group_level+row_filters+'>'+chtd+gtd+ctd+'</tr>';
        //if(typeof Pnk.tr_fn !== 'undefined') Pnk.tr_fn(newtr);
		gtr=gtr+newtr;
	}

	return gtr;
}

function pnk_load_thead(id,data)
{
	var pnk_srcid=pnk_src[ $('#'+id).attr('pnk-src') ];


	var orderby=$('#'+id).attr('order-by');
	var gth="";

  if(typeof pnk_srcid['bulk_actions']!="undefined"){
    // Add checkbox
		gth=gth+'<th style="width:28px;"><i class="fa fa-square-o bulk_checkbox" aria-hidden="true"></i></th>'; // Leave a column for the checkboxes
	}//<input type="checkbox">

	for(var index in data['fields']) if (typeof data['fields'][index] !== 'function') {
        var field = data['fields'][index];
		var df=pnk_srcid['fields'][field];
    	var sprht='';var grbut='';var thclass="";var thstyle="";
        if(typeof df.title !== 'undefined') title = df.title; else title = field;

		thclass="sorting";
        sprht='<i class="pnk-icon pnk-sorti fa fa-sort"></i>';

		if(typeof df['show'] !== "undefined") if(df['show']==false) thclass+=' col-hide';

		if(df['thclass']) thclass += " "+df['thclass'];
        if(typeof df['style'] !== "undefined") thstyle=' style="'+df['style']+'"';
        if(df['type']) if(df['type']=="number") thclass += ' col-number';

        sortic='fa fa-sort';
        if( orderby == field+'_a') sortic = 'sort-asc fa fa-sort-down';
        if( orderby == field+'_d') sortic = 'sort-desc fa fa-sort-up';

        sorti='<i class="pnk-icon pnk-sorti '+sortic+'" col="'+field+'"></i>';

        var rowspan="";

		gth=gth+'<th col="'+field+'"'+thstyle+rowspan+' class="'+thclass+'">'+sorti+title+'</th>';
	}

	if(typeof pnk_srcid['commands']!="undefined"){
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
		if(typeof data['filtered']!='undefined') if(typeof data['filtered'][field]!='undefined'){
			if(Array.isArray(data['filtered'][field])){
				if(typeof data['filtered'][field][0]!='undefined') v[1]=data['filtered'][field][0];
				if(typeof data['filtered'][field][1]!='undefined') v[2]=data['filtered'][field][1];
			}else v[0]=data['filtered'][field];
		}
		gsr=gsr+'<td col="'+field+'" style="max-width:'+$('#'+id+' thead th[col='+field+']').width()+'px">'+createFilter(df,v)+'</td>';
	}


	if(typeof pnk_srcid['commands']!="undefined"){
		gsr=gsr+'<td><i class="fa fa-search pnk-com com-search"></i><i class="fa fa-refresh pnk-com com-refresh"></i></td>';
	}
	return '<tr class="pnk-srow">'+gsr+'</tr>';
}


function pnk_cv(cv,fc,rv)
{
	// print the cell
	if($.isArray(cv)) dv=''; else dv=cv;


	if(cv!=''){
	//////// Check the type
	if(typeof fc['type'] !== "undefined" ){
		if(fc['type']=="number") if(typeof numeral == 'function') dv=numeral(cv).format('0,0.00');
		if(fc['type']=="checkbox") return '<p><i class="fa fa'+(cv==1?'-check':'')+'-square-o"></i></p>';
		if(fc['type']=="level"){
            var clevels = cv.split("-");
            if(typeof fc['levels'] !== "undefined" ) if(fc['levels'] > clevels.length) dv = '<i class="fa fa-plus">'+cv;
        }
	}

	/////// Numeral plugin
	if(typeof fc['numeral'] !== "undefined" ){
		if(typeof numeral == 'function') dv=numeral(cv).format(fc['numeral']);
	}

	/////// Roles
	if(typeof fc['type'] !== "undefined" ) if(fc['type']=="roles"){
		return '<p>'+cv+' <button class="pnk-roles"><i class="fa fa-key"></i></button></p>';
	}

	/////// Gallery
	if(typeof fc['type'] !== "undefined" ) if(fc['type']=="gallery"){
		return '<p>'+cv+' <button class="pnk-gallery"><i class="fa fa-camera"></i></button></p>';
	}

	/////// Option list
	if(typeof fc['options'] !== "undefined" ){
		dv=fc['options'][cv];
		if(typeof fc['png_url'] != "undefined" ) dv='<img src="'+fc['png_url']+cv+'.png">';
		if(typeof fc['bg-options'] != "undefined" ) dv='<div style="padding:5px;color:white;background:'+fc['bg-options'][cv]+'" class="bg-option">'+dv+'</div>';
	}

	/////// Date format
	if(typeof fc['dateFormat'] !== "undefined" ){
		dv= $.datepicker.formatDate( fc['dateFormat'], new Date( cv.replace(/-/g, '/') ) );
	}

	/////// Update buttons
	if(typeof fc['up-button'] !== "undefined" ){
		$.each(fc['up-button'], function( index, value ) { if(index!=cv) dv=dv+' <button class="btn btn-default bg-option up-button" value="'+index+'">'+value+'</button>'; });
	}

	}

	/////// Eval
	if(typeof fc['eval'] !== "undefined" ) eval(fc['eval']);//alert(fc['eval']);//

  /////// Parcial
	if(typeof fc['partial'] !== 'undefined') dv=dv+' <i class="fa fa-chevron-down partial-dr" partial-src="'+fc['partial'][0]+'" partial-id="'+fc['partial'][1]+'"></i>';

	//return '<p>'+dv+'</p>';
  return dv;
}

//////////// Pagination
////////////
function pagination(curPage, totalPage)
{
	var gtr='';
	var firstPage=curPage-4;
	var lastPage=curPage+3;
	if(firstPage<2) firstPage=1; else gtr+='<li><a href="#">1</a></li>';//'<button class="btn btn-default">1</button>...';
	if(lastPage<=firstPage+8) lastPage=firstPage+8;
	if(lastPage>totalPage) lastPage=totalPage;

	for(var i=firstPage; i<lastPage+1; i++){
    if(i==curPage) gtr+='<li class="active"><a href="#">'+i+'</a></li>'; else gtr+='<li><a href="#">'+i+'</a></li>';
    //if(i==curPage) gtr+='<button class="btn btn-default disabled">'+i+'</button>'; else gtr+='<button class="btn btn-default">'+i+'</button>';
	}
	if(lastPage<totalPage) gtr+='<li><a href="#">'+totalPage+'</a></li>';

	return gtr;
}
$(document).on('click','.pnk-table table tfoot tr td .pagination a',function(event){
  event.preventDefault();
	pnk_reload($(this).closest('table').attr('id'),'cur-page', $(this).html() );
});

//////////// Th click
////////////
$(document).on('click','table thead tr th',function(){
	var tid=$(this).closest('table').attr('id');
	var col=$(this).attr('col');
	if( $(this).hasClass('sorting') ){
		if( $(this).closest('table').attr('order-by')==col+'_d' ) pnk_reload(tid,'order-by',col+'_a'); else pnk_reload(tid,'order-by',col+'_d');
	}
	if( $(this).hasClass('grouping') ){
		//if( $(this).closest('table').attr('group-by')==col ) pnk_reload(tid,'group-by',''); else pnk_reload(tid,'group-by',col);
	}
});




$(document).on('click','.pnk-table table tr td p .pnk-roles',function()
{
	// Roles Dialog
	var cell=$(this).closest('td');
	var table_src=$(cell).closest('table').attr('pnk-src');
	var df=pnk_src[table_src]['fields'][$(cell).attr('col')];
	var cw=$(cell).width();

	if(df['type']=='roles'){
		$.ajax({ url: pnkPath + "pnk/keys", dataType: "json",type: 'post',
		data: { 'kt':$(cell).attr('col'),'f' : df['fields'][0], 'fv' : $(cell).closest('tr').attr('row-id'), 'k' : df['fields'][1]  }, success: function(roles) {

		var temp=$(cell).attr('value');
		var sopt='';
		for(var v in df['options']){
			checked='';
			if(jQuery.inArray(v,roles)>-1) checked='checked';
			if(v==temp) selected=' selected'; else selected='';
			sopt+='<input type="checkbox" id="rc'+v+'" name="rc'+v+'" v="'+v+'" '+checked+'><label for="rc'+v+'">'+df['options'][v]+'</label><br>';
		}

		var dialog_buttons = {};

		dialog_buttons[_e('Update')]=function() {
			var keys='';
			$(this).find('[type=checkbox]').each(function(index){
				if($(this).is(':checked')){
					if(keys!="") keys+=',';
					keys+= '"'+index+'":"'+$(this).attr('v')+'"';
				}
			});
			$.ajax({ url: pnkPath + "pnk/keys?t="+table_src+"&update", dataType: "json",type: 'post',
				data: { 'keys':'{'+keys+'}','kt':$(cell).attr('col'),'f' : df['fields'][0], 'fv' : $(cell).closest('tr').attr('row-id'), 'k' : df['fields'][1]  },
				success: function(nk){ $(cell).html( pnk_cv( nk,df ) ); } // "<p>"+pnk_cv( nk,df )+"</p>"
			});
			$(this).dialog("close");
		};
		dialog_buttons[_e('Cancel')]=function() { $(this).dialog("close"); };

		var newDiv = $(document.createElement('div'));
		$(newDiv).html(sopt);
		$(newDiv).attr('title',df['title']).dialog({dialogClass: "pnk-dialog",buttons: dialog_buttons });

		}});
		return;
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
	$(newDiv).html('<iframe id="fileframe" src=pnkPath + "pnk-gallery.php?t='+t+'&f='+f+'&id='+id+'" style="width:800px;height:66%;">');
	$(newDiv).attr('title',df['title']).dialog({dialogClass: "pnk-dialog w60" }); //
});



//////////// Td click
///////////

$(document).on('click','.pnk-table table tbody .main-row td',function(){
	var pid=$(this).closest('.pnk-table').attr('pnk-src');
	if(typeof pnk_src[pid]['td-command']!='undefined') $(this).closest('.pnk-table').find('.com-'+pnk_src[pid]['td-command']).click();

	// Turn cell into input
	if(pnk_src[pid]['edit-cells']){
		remove_inputs();
		if($(this).children('.pnk-input').length==0) cellInput(this);
	}
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
	if(typeof $(this).val()!='undefined') val=$(this).val();
	if(typeof groupfor!='undefined') if($(this).val()=='')  groupfor.css('visibility','hidden'); else groupfor.css('visibility','visible');
	pnk_reload(id,'group-by', val );
});


$(document).on('change','.pnk-table .pnk-groupfor',function(){
	var pnksrc=$(this).closest('.pnk-table').attr('pnk-src');
	var table=$(this).closest('.pnk-table').find('table[pnk-src='+pnksrc+']');
	var id=table.attr('id');
	table.attr('cur-page','1');
	var val='';
	if(typeof $(this).val()!='undefined') val=$(this).val();
	pnk_reload(id,'group-for', val );
});



// Translation
function _e(phrase)
{
	if(typeof lang_array!=="undefined") if(typeof lang_array[phrase]!=="undefined") return lang_array[phrase];
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
	pnk_create(dr_pnk,ptableid+'-partial');
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
		pnkPath + "pnk/uc?t="+t,
		{ 'col' : col, 'v' : v, 'rid' : tr.attr('row-id') },
		 function(data) {
            xx = pnk_load_rows( pnk_src[t] ,data );
			tr.replaceWith( xx );
			tr.attr("class","main-row");
            if (Pnk.load_fn !== 'undefined') Pnk.load_fn();
            $('.pnk-com').tooltip();
		}
	,'json');
});
$(document).on('click','.pnk-table .up-button',function()
{
	var tr= $(this).closest('tr');
	var cell=$(this).closest('td');
	var t=$(this).closest('.pnk-table').attr('pnk-src');
	//var tid=$(this).closest('table').attr('id');
	var col=cell.attr('col');
	var v=$(this).attr('value');
	$.ajax({
		url: pnkPath + "pnk/uc?t="+t,
		data: { 'col' : col, 'v' : v, 'rid' : tr.attr('row-id') },
		dataType: "json",
		type: 'post',
		success: function(data) {
			tr.replaceWith( pnk_load_rows( pnk_src[t] ,data ) );
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
		url: pnkPath + "pnk/delete?t="+t,
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
	var tlist= pnk_src[tsrc]['commands']['list'];
	var colspan = t.find(" thead tr:first th").length;

	if(typeof tlist=='undefined') return;
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
		pnk_create( $(this), 'pnk-l'+index);
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

function edit_dialog(tid,action,id=""){
	var sopt='';
	var t=$("#"+tid).closest('.pnk-table').attr('pnk-src');

	var editDiv = $("#"+tid).closest('.pnk-table').find('.pnk-edit');
	switch_div($("#"+tid).closest('.pnk-table'),'.pnk-edit');

  var row_id="";
	if(id==""){
		dbupt=_e('Save');
		dbt=_e('New Registry');
	}else{
		dbupt=_e('Update');
		dbt=_e('Edit Registry');
    row_id=id;
	}
  if(action=="clone"){
    dbupt=_e('Save');
		dbt=_e('Clone Registry');
    row_id="";
  }

	btns='<div style="width: 100%;text-align:right;display: inline-block;margin-bottom:8px;border-top:1px solid #ccc"><br><br><button class="btn btn-primary pnk-edit-save" type="button" row-id="'+row_id+'" tid="'+tid+'">'+dbupt+'</button> <button class="btn btn-danger pnk-edit-cancel" type="button">'+_e('Cancel')+'</button></div>';

	// Add input for every field
	$.ajax({
		url: pnkPath + "pnk/edit?t="+t, data: { 'id' : id }, dataType: "json",type: 'post',
		success: function(data) {

			for(i=0; i<data.fields.length; i++) {
				field=pnk_src[t]['fields'][ data.fields[i] ];
        if(typeof field['edit']!='undefined') if(field['edit'] == false) continue;
				if(typeof field['title']!='undefined') title=field['title']; else title=data.fields[i];
				sopt=sopt+'<div class="update-div" col="'+data.fields[i]+'"><label for="'+data.fields[i]+'">'+title+'</label>'+createInput(field,data.rows[0][i])+'</div>';
			}


			editDiv.html('<div style="width: 100%;display: inline-block;margin-bottom:8px;border-bottom:1px solid #ccc"><span class="pnk-edit-title">'+dbt+'</span></div>'+sopt+btns);
			editDiv.attr('tid',tid);
/*
			for(i=0;i<data.fields.length;i++) if(typeof pnk_src[t]['fields'][ data.fields[i] ].partial != 'undefined'){
				field=pnk_src[t]['fields'][ data.fields[i] ];
				filters=field.partial[1]+'='+id;
				editDiv.append('<div class="pnk-table" tid="'+i+'" pnk-src="'+field.partial[0]+'" filters="&'+filters+'" ></div>');
			}

			editDiv.find('.pnk-table').each(function(index){
				//pid=$(this).attr('tid');
				pnk_create($(this),'pnk-ed'+index);
			});*/

			//editDiv.find(".selectpicker").selectpicker();

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
      //if(typeof $.fn.select2 != 'undefined') $(".pnk-select").select2();
			if(typeof($.fn.datetimepicker) == "function") $(".datetimepicker").datetimepicker({format: 'Y-m-d H:i:s'});

		}
	});
}
$('body').on('click','.pnk-edit-save',function(e){
	var id=$(this).attr('row-id');
	var t=$(this).closest('.pnk-table').attr('pnk-src');
	var tid=$(this).attr('tid');
	var data='{"'+id+'":{';
	var dcv='';
	$(this).closest('.pnk-edit').find( '.update-div' ).each( function() {
		if(dcv!='') dcv+=',';
		dcv+='"'+$(this).attr('col')+'":"'+$('.pnk-input',this).val()+'"';
	});

	// Read the filters and add them in data
	filters=$(this).closest('.pnk-table').attr('filters');
	if(typeof filters!="undefined") if(id==''){
		farray=filters.split("&");
		for (i = 0; i < farray.length; i++) {
			frow=farray[i].split('=');
			if(dcv!='') dcv+=',';
			dcv+='"'+frow[0]+'":"'+frow[1]+'"';
		}
	}
	data+=dcv+'}}';

	data =jsonEscape(data);

		$.ajax({
			url: pnkPath + "pnk/update?t="+t,
			data: { 'erows' : data },
			cache:  false ,
			dataType: "json",
			type: 'post',
			success: function(data) {
				if(id!=""){
					var tr=$('#'+tid+' tr[row-id="'+id+'"]');
					tr.replaceWith( pnk_load_rows( pnk_src[t], data ));
          $('.pnk-com').tooltip();
					var newtr=$('#'+tid+' tr[row-id="'+id+'"]');
					newtr.addClass('main-row');
					var tbg=newtr.css("background-color");
					newtr.css("background-color","#ffff00");
					newtr.animate({backgroundColor: rgb2hex(tbg)}, 300 );
				}else{
					$('#'+tid+' tbody').prepend( pnk_load_rows( pnk_src[t], data ) );
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

	switch_div( $(this).closest('.pnk-table'),'.pnk-table-list');
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
		if(typeof $(this).parent().attr('col')=='undefined') return;
		if(typeof $(this).attr('colf') != 'undefined') colf=$(this).attr('colf'); else colf='';
		if($(this).val() != ''){
			findex=$(this).closest('div').attr('col')+colf;
			updf[findex]=$(this).val();
			filters=filters+'&'+findex+'='+$(this).val();
		}
	});

	pnk_reload(pnk_id,'filters',filters);

});

$(document).on('click','.pnk-table .com-refresh',function()
{
	$(this).closest('.pnk-searchbox').find('div[col] .pnk-input').each( function( index ) {
		$(this).val('').change();
	});
	var pnk_id=$(this).closest('.pnk-table').find('table[pnk-src]').attr('id');
	pnk_reload(pnk_id,'filters','');
});

////  Method to return the field Input
function createInput(df,v){
    let arrv = new Array();
	// Select Dropdown
	if(df['options']) if(df['type']!="roles"){
		var sopt='';
		opn = 0;
		if(v=='') sopt='<option value="">-</option>';
    if(df['type']=='joins') arrv=v.split(","); else arrv[0]=v;

		for(var temp in df['options']){
			if(arrv.indexOf(temp)>-1) selected=' selected'; else selected='';
			sopt+='<option value="'+temp+'"'+selected+'>'+df['options'][temp]+'</option>';
			opn++;
		}

		if( opn >5 ) d_l_s = ' data-live-search="true"'; else d_l_s = '';

    if(df['type']=='joins') return '<select multiple class="pnk-input pnk-select" '+d_l_s+'>'+sopt+'</select>';
    return '<select class="pnk-input pnk-select" '+d_l_s+'>'+sopt+'</select>';
	}

  // Joins - Multiselect
  if(df['type']=='joins'){
    field_o = "<option value='1'>One</option><option value='2'>Two</option><option value='3'>three</option>";
    return '<select multiple>'+field_o+'</select>';
  }

	// Checkbox
	if(df['type']=="checkbox"){
		var checked='value="0"';
		if(v==1) checked='value="1" checked';
		return '<input type="checkbox" class="pnk-input" '+checked+'>';
	}

  // Textarea
	if(df['type']=='text') return '<textarea class="pnk-input">'+v+'</textarea>';
	// Date
	if(df['type']=='date') return '<input class="pnk-input datepicker" value="'+v+'">';
	// Datetime
	if(df['type']=='datetime') return '<input class="pnk-input datetimepicker" value="'+v+'">';

	// Normal Input
	var xclass="";
	return '<input type="text" class="pnk-input" value="'+v+'">';
}

////  Method to return the field Filter
function createFilter(df,v){

	// Select Dropdown
	if(df['options']) if(df['type']!="roles"){
		var sopt='<option value="">-</option>';
		for(var temp in df['options']){
			if(v[0]==temp) selected=' selected'; else selected='';
			sopt+='<option value="'+temp+'"'+selected+'>'+df['options'][temp]+'</option>';
		}
		return '<select class="pnk-input pnk-select">'+sopt+'</select>';
	}
	if(df['search-options']){
		var sopt='';
		for(var temp in df['search-options']){
			if(v[0]==temp) selected=' selected'; else selected='';
			sopt+='<option value="'+temp+'"'+selected+'>'+df['search-options'][temp]+'</option>';
		}
		return '<select class="pnk-input">'+sopt+'</select>';
	}

	// Checkbox
	if(df['type']=="checkbox"){
		if(v[0]=="1") sel1=" selected>"; else sel1=">";
		if(v[0]=="0") sel0=" selected>"; else sel0=">";
		return '<select class="pnk-input"><option value="">-</option><option value="1"'+sel1+_e('Yes')+'</option><option value="0"'+sel0+_e('No')+'</option></select>';
	}

	// Date
	var xclass="";
	if(df['type']=="date"){
		if(df['searchbox']=="period")
			return '<input colf="_from" class="pnk-input datepicker" placeholder="'+_e('from')+'" value="'+v[1]+'">-<input colf="_to" class="pnk-input datepicker" placeholder="'+_e('to')+'" value="'+v[2]+'">';
		//if(df['searchbox']=="month");
		return '<input class="pnk-input datepicker" value="'+v[0]+'">';
	}

	if(df['searchbox']=="range"){
		return '<input colf="_from" class="pnk-input" placeholder="'+_e('from')+'" value="'+v[1]+'">-<input colf="_to" class="pnk-input" placeholder="'+_e('to')+'" value="'+v[2]+'">';
	}

	// Text
	var xclass="";
	return '<input type="text" class="pnk-input" value="'+v[0]+'">';
}



var Pnk = {
  tools: new Array(),
  commands: new Array()
};

/////////  C O M M A N D S
/////////////////////////////

$(document).on('click','.pnk-com',function(){
  var e = new Array();
  var command = $(this).attr("com");

  for(i in Pnk.commands) if($(this).hasClass("com-"+i)) command=i;

  e.src = $(this).closest('table').attr('pnk-src');
  e.table_id = $(this).closest('table').attr('id');
  e.row_id = $(this).closest('tr').attr('row-id');
  e.row = $(this).closest('tr');
  e.tr = e.row;
  Pnk.commands[command].fn(e);
});

Pnk.commands.edit = { fa: "pencil", title: "Edit", fn: function(e){ edit_dialog(e.table_id,"edit",e.row_id); }};
Pnk.commands.clone = { fa: "copy", title: "Clone", fn: function(e){ edit_dialog(e.table_id,"clone",e.row_id); }};
Pnk.commands.pdf = { fa: "file", title: "Pdf", fn: function(e){ window.open(pnkPath + "pdf.php?t="+e.src+"&id="+e.row_id); }};
Pnk.commands.delete = { fa: "trash-o", title: "Delete", fn: function(e){
	if ( confirm(_e("Delete registry?"))) pnk_delete_row(e.tr);
}};

/////////  T O O L B O X
///////////////////////////

$(document).on('click','.pnk-tools button[tool]',function() {
  var e = new Array();
  var tool = $(this).attr("tool");
  e.table_id = $(this).parent().attr('table-id');
  e.pnk_table = $(this).closest('.pnk-table');
  e.tr_list = e.pnk_table.find('tr.selected');
  e.tr_array = new Array();
  e.tr_list.each(function(){ e.tr_array.push( $(this).attr('row-id') ); });
  e.tr_ids = e.tr_array.toString();
  Pnk.tools[tool].fn(e);
});

Pnk.tools.add = { fa: "plus", title: "New", fn: function(e){ edit_dialog(e.table_id,"new"); }};
Pnk.tools.pdf = { fa: "file-text", title: "Pdf", fn: function(e){ pnk_window_open(e,"pdf"); }};
Pnk.tools.csv = { fa: "download", title: "Csv", fn: function(e){ pnk_window_open(e,"csv"); }};
Pnk.tools.xls = { fa: "download", title: "Xls", fn: function(e){ pnk_window_open(e,"xls"); }};
Pnk.tools.delete = { fa: "trash", title: "Delete", fn: function(e){
  if(e.tr_list.length>0) {
    if ( confirm(_e("Delete selected registries?")) == false) return;
    e.tr_list.each(function(){
      pnk_delete_row($(this));
    });
}}};
Pnk.tools.search = { fa: "search", title: "Search", fn: function(e){
  e.pnk_table.find('.pnk-srow').toggle();
  e.pnk_table.find('.pnk-searchbox').toggle();
}};
Pnk.tools.clipboard = { fa: "copy", title: "Clipboard", fn: function(e){
  var xls='<textarea style="width:100%">'+e.pnk_table.find(".select-result").text()+'</textarea>';
	$(xls).attr('title',_e("Select Data")).dialog(  );//{dialogClass: "pnk-dialog"}
}};



function pnk_window_open(e,file) {
  var id = e.table_id;//$(this).closest('.pnk-tools').attr('table-id');
	var t = $('#'+id).attr('pnk-src');
	var orderby=$('#'+id).attr('order-by');
	var groupby=$('#'+id).attr('group-by');
	var filters=$('#'+id).attr('filters');

	window.open(pnkPath + "pnk/pnkfile?t="+t+"&file="+file+"&orderby="+orderby+"&groupby="+groupby+filters);
}



$(document).on("click",".col-group",function(){
  var pf = $(this).parent().attr("filters");
  var group_level = $(this).parent().attr("group_level");
  if(typeof group_level == 'undefined') group_level = 0;
  var id = $(this).closest("table").attr("id");
  var t = $(this).closest("table").attr("pnk-src");
  if(!pf) pf="";
  var filters = pf + "&" + $(this).attr("col") + "=" + $(this).attr("value");
  var _this = $(this);
  var groupby = "";

  if(group_level == '') group_level = 0;

  // Get the rows from the source file
	$.getJSON( pnkPath + "pnk/list?t="+t+groupby+"&page=1"+filters, function( data ) {
    var new_rows = pnk_load_rows(pnk_src[t], data, { filters:filters, group_level:(group_level++) });
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

  if(!$(this).hasClass('checked')){
    $(this).closest('table').find('tr').addClass("selected");
    $(this).closest('table').find('.tr_checkbox').addClass("checked fa-check-square-o").removeClass("fa-square-o");

    $(this).addClass("checked fa-check-square-o").removeClass("fa-square-o");
  }else{
    $(this).closest('table').find('tr').removeClass("selected");
    $(this).closest('table').find('.tr_checkbox').removeClass("checked fa-check-square-o").addClass("fa-square-o");

    $(this).removeClass("checked").removeClass("fa-check-square-o").removeClass("fa-minus-square-o").addClass("fa-square-o");
  }
})
