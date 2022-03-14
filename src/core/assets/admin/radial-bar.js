Vue.component('radial-bar', {
  template: '<div :style="{margin:"50px auto", width:width, height:width, background:color, \'border-radius\':\"50%\", position:\"absolute\"}">\
  <style>@keyframes radiusfill {0% {transform: rotate(0deg);}100% {transform: auto;}}</style>\
  <div>\
    <div class="mask full r70" :style="maskFullStyle">\
      <div class="fill r70" :style="fillStyle"></div>\
    </div>\
    <div class="mask half" :style="maskStyle">\
      <div class="fill r70" :style="fillStyle"></div>\
    </div>\
    <div class="inside-circle"></div>\
  </div>\
</div>\
',
  props: ['value','width'],
  data: function() {
    circleStyle = {
      position: "absolute",
      'border-radius': "50%",
      transform: "rotate(0deg)",
      transition: "transform 1s",
      width:this.width,
      height:this.width
    }
    maskStyle = circleStyle
    maskStyle.clip = 'rect(0px, '+this.width+', '+this.width+', 50%)'
    fillStyle = circleStyle
    fillStyle.clip = 'rect(0px, 50%, '+this.width+', 0px)'
    fillStyle['background-color'] = '#9e00b1'
    fillStyle.animation = "animation: radiusfill ease-in-out 1s;"
    fillStyle.transform = 'rotate('+(180/this.value)+'deg)'
    maskFullStyle = maskStyle
    maskFullStyle.animation = "animation: radiusfill ease-in-out 1s;"
    maskFullStyle.transform = 'rotate('+(180/this.value)+'deg)'
    return {
      maskStyle: maskStyle,
      fillStyle: fillStyle,
      maskFullStyle: maskFullStyle,
      color:"red"
    }
  }
})
