
gtableFieldDisplay.title = function(rv) {
  if(rv.publish==0) return rv.title;
  return '<a target="_blank" href="'+rv.slug+'">'+rv.title+'</a>'
}
