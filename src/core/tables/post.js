
gtableFieldDisplay.thumbnail = function(rv) {
  if(rv.thumbnail==null) return ''
  return '<img src="lzld/thumb?src='+rv.thumbnail+'&media_thumb=80"></img>'
}

gtableFieldDisplay.title = function(rv) {
  if(rv.publish==0) return rv.title;
  return '<a target="_blank" href="blog/'+rv.id+'">'+rv.title+'</a>'
}
