
gtableFieldDisplay.title = function(rv) {
  if(rv.publish==0) return rv.title;
  la = ''
  if (rv.language && rv.language!=='') la=rv.language+'/'
  return '<a target="_blank" href="'+la+rv.slug+'">'+rv.title+'</a>'
}

gtableCommand.page_seo = {
  fa: "search",
  label: "SEO",
  permission: "update",
  fn: function(table,irow) {
    gtableCommand.edit_popup.fn(table,irow)
  }
}
