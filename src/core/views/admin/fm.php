<?=view::cssAsync('lib/CodeMirror/codemirror.css')?>
<script src="lib/CodeMirror/codemirror.js"></script>
<script src="lib/CodeMirror/javascript.js"></script>
<script src="lib/CodeMirror/css.js"></script>
<script src="lib/CodeMirror/xml.js"></script>
<script src="lib/CodeMirror/php.js"></script>
<script src="lib/CodeMirror/htmlmixed.js"></script>


<style>
.filename {

}
.closetab {
    opacity: 0.2;
}
.closetab:hover {
    opacity: 1;
}
</style>

<div id='fm_filecontents'></div>

<div class="g-nav g-tabs" id="fm_tabs"><li><a href="#tab_fm_table" id="tab_a_fm">Files</a></li></div>

<div class="tab-content gs-12" id="fm_tab_contents">
    <div id="tab_fm_table" style="display: block;">
        <input class="g-input fullwidth" id='path_bar' value="<?=$c->path?>" disabled/>
		<table id='fm_table' class="g-table unbordered">
			<thead>
				<tr><th style='width:32px'><th>Filename<th>Size<th>Modified<th>Permissions
			</thead>
			<tbody>
			</tbody>
		</table>
    </div>

</div>


<script>
requiredRes = new Array()
var myCodeMirror = new Array();
var saveFilePath;

g.require(['lib/jquery/jquery-3.3.1.min.js'], function(){
    refresh_fm_table('');

    $(document).on('click','#fm_tabs a', function(){
        return false
    })
    $(document).on('click','.filename', function(event){
        saveFilePath=''
		ext = this.getAttribute('data-type')
		filepath = this.getAttribute('data-path')
        event.preventDefault()

        if(ext=='') {
			refresh_fm_table(filepath);
			return false
		}

		realpath = $('#path_bar').val() + '/'+ filepath

		if(['php','html','htm','xml','js','txt','json','css','htaccess','yaml','md'].includes(ext)) {
			$.post('fm/read', {action:'read',path:realpath},function(data){
				newid = 'tab_'+filepath.split('.').join('_');
				g('#fm_tabs').append('<li><a href="#'+newid+'" id="a_tab_'+newid+'">'+filepath+' <i class="fa fa-times-circle closetab"></i></a></li>');
				g('#fm_tab_contents').append('<div id="'+newid+'" style="display: block;"></div>');
				g.el(newid).innerHTML = '<textarea id="textarea" class="fullwidth" style="height:450px">'+data+'</textarea>'
                g.el(newid).innerHTML += '<hr><a class="btn btn-success" onclick="savefile(\''+realpath+'\')">Save</a>'
				g.el('a_tab_'+newid).click();
				mode=ext
                if(ext=='js') mode='javascript'
                if(ext=='css') mode='css'
                if(ext=='php') mode='php'
                if(['phtml','html','htm'].includes(ext)) mode='htmlmixed'
				//if(ext=='html') mode='htmlmixed'
                saveFilePath=realpath
				myCodeMirror[realpath] = CodeMirror.fromTextArea(g('#'+newid+' textarea').all[0],{lineNumbers:true,mode:mode});
			})
		}

		if(['jpg','jpeg','gif','png','svg','ico','tiff'].includes(ext)) {
			g.dialog({body:'<img src="fm/efile?f='+realpath+'" style="margin:auto;float:middle">',buttons:''})
		}
        return false
    })

    function refresh_fm_table(path) {
        console.log(path)
        saveFilePath=''
        if (path === undefined) path = ''; else path = g.el('path_bar').value + '\\'+path;

        $.post('?c=fm&action=dir', {action:'dir',path:path},function(data){
            g('#fm_table tbody').html('');
			g.el('path_bar').value=data.path;
			file = data.files;
            for (i=0; i<file.length; i++) {
                tdfilename = '<tr class="filename" data-type="'+file[i].ext+'" data-path="'+file[i].name+'"><td>'+get_file_icon(file[i].ext)+'<td>'+file[i].name;
                if(file[i].mode>33278) td_file_mode="<td style='color:green'>"+file[i].mode; else td_file_mode='<td>'+file[i].mode;
                g('#fm_table tbody').append(tdfilename+'<td>'+file[i].size+'<td>'+file[i].mtime+td_file_mode);
            }
        },'json')
    }

	function get_file_icon(ext){
		icons={txt:'file-text',php:'file-text',jpg:'image',png:'image',gif:'image'}
		icon='file';
		if(typeof icons[ext]!='undefined') icon = icons[ext];
		if(ext=='') icon='folder-o';
		return '<i class="fa fa-'+icon+'"></i>'
	}
})

g.click('.g-tabs li a',function(event){

    g(event.target).findUp('.g-tabs').children().removeClass('active');
    g(event.target).findUp('li').addClass('active');
	if(typeof event.target.href=='undefined') return;

    hash=event.target.href.split('#');
    if(typeof hash[1]!=='undefined') if(hash[1]!==''){
        x='#'+hash[1];
        g(x).parent().children().style('display','none');
        g(x).style('display','block');
    }
    return false;
})
g.click('.g-tabs .closetab',function(event){
    var x=g(event.target).findUp('li')
    g(event.target).findUp('.g-tabs').find('li:first-child a').all[0].click();
    event.preventDefault()
    x.remove();
    return false;
})

function savefile(path) {
    if(saveFilePath=='') {
        alert("Select a file from the tabs to save");
        return
    }
    $.post('fm/save', {contents:myCodeMirror[path].getValue(),path:path},function(msg){
        if(msg=='') msg="File saved successfully"
        alert(msg);
    })
}
</script>
