window.onload = new function() {
  // Don't lazy load on IE8.
  var images = document.getElementsByTagName('img');
  for (var i = 0; i < images.length; i++) {
    images[i].setAttribute('src', images[i].getAttribute('data-src'));
    images[i].removeAttribute('data-src', '');
  }
};