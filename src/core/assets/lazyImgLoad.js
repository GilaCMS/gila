lazyImgLoad();
window.onload = function() {
  lazyImgLoad();
}
window.addEventListener('scroll', lazyImgLoad);
window.addEventListener('touchmove', lazyImgLoad); //touch devices

function lazyImgLoad() {
  imgs = document.getElementsByClassName('lazy');

    for(i=0; i<imgs.length; i++) {
      el=imgs[i]
	  r = el.getBoundingClientRect();
	  if ( r.bottom >= 0 &&
	    r.right >= 0 &&
	    r.top <= (window.innerHeight || document.documentElement.clientHeight) &&
	    r.left <= (window.innerWidth || document.documentElement.clientWidth) ) {
	      if (el.getAttribute('data-src')) {
	        el.src = imgs[i].getAttribute('data-src');
	        el.removeAttribute('data-src');
	      }
          if (el.getAttribute('data-bg')) {
	        el.style.background = imgs[i].getAttribute('data-bg');
	        el.removeAttribute('data-bg');
	      }
          if (el.getAttribute('data-image')) {
	        el.style.backgroundImage = imgs[i].getAttribute('data-image');
	        el.removeAttribute('data-image');
	      }
          if (el.getAttribute('data-load')) {
            let xhttp = new XMLHttpRequest();
            let _el = el;

            xhttp.onreadystatechange = function() {
              if (this.readyState == 4 && this.status == 200) {
                _el.innerHTML = this.responseText;
                _el.removeAttribute('data-load');
              }
            };

            xhttp.open("GET", el.getAttribute('data-load'), true);
            xhttp.send();
          }
	  }
    }
}
