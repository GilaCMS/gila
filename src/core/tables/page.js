
gtableFieldDisplay.title = function(rv) {
  if(rv.publish==0) return rv.title;
  la = ''
  if (rv.language && rv.language!=='') la=rv.language+'/'
  return '<a target="_blank" href="'+la+rv.slug+'">'+rv.title+'</a>'
}
