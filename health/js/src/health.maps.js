/**
 * Generate an interactive Google map.
 */
function initGoogleMaps() {
  for (var i=0; i<Drupal.settings.health.maps.google.length; i++) {
    var location = {lat: Drupal.settings.health.maps.google[i].lat, lng: Drupal.settings.health.maps.google[i].long};
    var map = new google.maps.Map(
      document.getElementById(Drupal.settings.health.maps.google[i].id), {zoom: 16, center: location});
    new google.maps.Marker({position: location, map: map});
  }
}