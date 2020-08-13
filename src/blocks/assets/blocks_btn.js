
gtableCommand.blocks = {
  fa: "bars",
  title: "Blocks",
  fn: function(table,id){
      window.location.href = 'blocks/?t='+table.name+'&id='+id
  }
};

gtableCommand.blocks_popup = {
  fa: "bars",
  label: "Blocks",
  fn: function(table,irow) {
    href = 'blocks/popup?t='+table.name+'&id='+irow;
    html = '<iframe src="'+href+'" style="width:100%; border:none; height:90vh;margin:0">'
    g.dialog({class:'overlay', body:html, type:'modal', id:'blocks_popup'})

      
    g_tinymce_options.height = 110;
    transformClassComponents()
  }
}
