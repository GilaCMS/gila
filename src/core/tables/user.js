
gtableFieldDisplay.photo = function(rv) {
  if(rv.photo==null) {
    let letter = rv.username.toUpperCase()[0];
    let color = ['red', 'lightseagreen', 'green', 'hotpink', 'darkorange', 'brown', 'blueviolet'][rv.id%7]
    return '<div style="box-shadow:0 0 3px grey; margin:6px; border-radius:50%;'+
    'width:50px;height:50px;background:'+color+'; color: white;\
    font-size: 34px; padding:6px; text-align: center; font-family: Arial;">'+letter+'</div>'
  }
  return '<div style="box-shadow:0 0 3px grey; margin:6px; border-radius:50%;'+
  'background-size:cover; width:50px;height:50px;background:url(lzld/thumb?src='+rv.photo+'&media_thumb=30); background-position: center;"></div>'
}
