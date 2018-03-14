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

  var base_url = window.location.origin;

  callFeed( base_url + '/news-and-events/health-alerts.xml' )
};

function callFeed( url ) {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {

      renderXml(this);
    }
  };
  xhttp.open("GET", url, true);
  xhttp.send();
}

function renderXml(xml) {
  var i;
  var xmlDoc = xml.responseXML;
  var div = '<div class="container">' +
    '<div class="health-alert-logo"></i>Health alert</div>\n' +
    '<div class="health-alert-content"><ul>';
  var x = xmlDoc.getElementsByTagName("item");
  for (i = 0; i < x.length; i++) {
    div += '<li><div class="ie-alert-date">' +
      x[i].getElementsByTagName("pubDate")[0].childNodes[0].nodeValue.substring(0, 10) +
      '</div><div class="ie-alert-title">' +
      '<a href="' + x[i].getElementsByTagName('link')[0].childNodes[0].nodeValue +'">' + x[i].getElementsByTagName("title")[0].childNodes[0].nodeValue +
      "</a></div></li>";
  }
  div += '</ul></div></div>';
  //console.log(div);
  document.getElementById('health-alert-ajax-container').innerHTML = div;
}
