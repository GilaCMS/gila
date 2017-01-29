
var loadedUrl = new Array();
var loadedRes = new Array();
var baseUrl = "res/";
/*
<script type="text/javascript" src="http://your.cdn.com/first.js"></script>
<script type="text/javascript">
loadJS("http://your.cdn.com/second.js", function(){
    //initialization code
});
</script>
*/
var requiredGroup = new Array();


require = function (res, fn){ loadRes(res, fn); };
loadRes = function (res, callback = function(){ return } ) {

  if(Array.isArray(res)) {
    var group_n = requiredGroup.length;
    requiredGroup[group_n] = { loaded:0, fn:callback };
    var gcall = "if(requiredGroup["+group_n+"].loaded == "+res.length+"){ requiredGroup["+group_n+"].fn(); alert('ok'); }else requiredGroup["+group_n+"].loaded++;";
    for(r=0; r<res.length; r++) {
      loadRes( res[r],function(){ group_callback(res.length,group_n); });
    };

    return;
  }

  var rRes = requiredRes[res];

  if(typeof rRes == 'undefined') {
    console.warn(res+" is not defined in require.js");
    requiredRes[res]={wjs:res};
    rRes = requiredRes[res];
    //return;
  }

  if(rRes.loaded == true) {
    callback();
    return;
  }

  if(rRes.dep) {
    loadRes(rRes.dep, function(){
      if(rRes.css) loadCSS(baseUrl+rRes.css);
      if(rRes.js) loadJS(rRes,callback);
      if(rRes.wjs) loadJS(rRes,callback);
      //callback();
    });
  }else{
    if(rRes.css) loadCSS(baseUrl+rRes.css);
    if(rRes.js) loadJS(rRes,callback);
    if(rRes.wjs) loadJS(rRes,callback);
  }

}

loadJS = function (res, callback = function(){ return } ) {
    //var url = res.js;
    if(typeof res.wjs == 'undefined') url = baseUrl+res.js; else url = res.wjs;

    var script = document.createElement("script")



    script.type = "text/javascript";

    if(res.loaded == true){
      callback();
      return;
    }else{
      if(typeof res.callbacks == 'undefined') res.callbacks = new Array();
      res.callbacks.push(callback);
      //console.log(res.js+" pushed");
    }

    if (script.readyState){  //IE
        script.onreadystatechange = function(){
            if (script.readyState == "loaded" ||
                    script.readyState == "complete"){
                script.onreadystatechange = null;
                res.loaded = true;
                for(i in res.callbacks) res.callbacks[i]();
            }
        };
    } else {  //Others
        script.onload = function(){
          res.loaded = true;
          //callback();
          for(i in res.callbacks) res.callbacks[i]();
        };
    }


  script.src = url;
  if(res.dev == 1) script.src += "?"+Math.random();

  if( res.loading != true ) {
    document.getElementsByTagName("head")[0].appendChild(script);
  	console.log(url+" loaded");
    res.loading = true;

  }//else callback();
}

loadCSS = function (url) {
	var fileref = document.createElement("link");
  fileref.setAttribute("rel", "stylesheet");
  fileref.setAttribute("type", "text/css");
  fileref.setAttribute("href", url);
  document.getElementsByTagName("head")[0].appendChild(fileref);
}

group_callback = function (n,group_n) {
  requiredGroup[group_n].loaded++;
  if(requiredGroup[group_n].loaded == n){
    requiredGroup[group_n].fn();
    console.log(requiredGroup[group_n].loaded+'!');
  } else {

    console.log(requiredGroup[group_n].loaded);
  }

}
