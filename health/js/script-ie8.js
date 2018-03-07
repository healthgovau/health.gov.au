window.onload = new function() {
  // Don't lazy load on IE8.
  var images = document.getElementsByTagName('img');
  for (var i = 0; i < images.length; i++) {
    if (images[i].hasAttribute('data-src')) {
      images[i].setAttribute('src', images[i].getAttribute('data-src'));
      images[i].removeAttribute('data-src', '');
    }
  }

  // Remove bottom padding on lazy loaded images.
  var imageWrappers = document.querySelectorAll('.image-wrapper');
  for (i = 0; i < imageWrappers.length; ++i) {
    imageWrappers[i].removeAttribute('style');
  }
};