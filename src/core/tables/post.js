
gtableFieldDisplay.title = function(rv) {
  if(rv.publish==0) return rv.title;
  la = 'la'
  if (rv.language && rv.language!=='') la=rv.language+'/'
  return '<a target="_blank" href="'+la+'blog/'+rv.id+'">'+rv.title+'</a>'
}
