


// nested lists
var oldContainer;
var group = $("ol.menu_nested").sortable({
  group: 'nested',
  handle: '.sort-item',
  afterMove: function (placeholder, container) {
    if(oldContainer != container){
      if(oldContainer) oldContainer.el.removeClass("active");
        container.el.addClass("active");
        oldContainer = container;
    }
  },
  //delay: 500,
  onDrop: function ($item, container, _super) {
    container.el.removeClass("active");
    _super($item, container);


 }
});

function add_item () {
    group.prepend('<li data-url="" data-title="Item"><i class="sort-item">||</i><input class="_title" value="Item"/><input class="_url" value=""/><i class="deletebtn fa fa-remove"></i><ol></ol></li>');
}


//$('ol li').attr("contenteditable",true);

$(document).on('change', '._title', function (){
    this.parentNode.attributes['data-title'].value = this.value;
});
$(document).on('change', '._url', function (){
    this.parentNode.attributes['data-url'].value = this.value;
});
$(document).on('click', '.deletebtn', function (){
    ol = this.parentNode.getElementsByTagName("OL")[0];
    if (ol.innerHTML != '') {
        response = confirm('Are you sure you want to delete this item an all its children?');
        if (response == false) return;
    }
    this.parentNode.remove();
});


widget_data_serialize = function () {
	var data = group.sortable("serialize").get();
  return JSON.stringify(data[0]);
}
