(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.health_alerts = {
    attach: function (context, settings) {

      var $alerts = $('.au-page-alerts', context);

      // Get the stored cookies indicating which alerts have been previously hidden.
      var cookies = getCookie('health.alerts');
      // If not already set, default to an empty array.
      if (cookies === null) {
        cookies = [];
      }
      // The cookie will be a text string separated by commas. Turn that into an array.
      else {
        cookies = cookies.split(',');
      }

      // Go through each alert.
      $alerts.each(function() {

        // Flag to indicate if we should show the alert or not.
        var keepHidden = false;

        var $alert = $(this);

        // Find the close button.
        // If the alert doesn't have one, we assume it is a normal message that can't be dismissed.
        var $close_button = $alert.find('.au-page-alerts__close');
        if ($close_button.length) {
          var nid = $close_button.attr('data-nid');

          for (var i = 0; i < cookies.length; i++) {
            if (cookies[i] === nid) {
              // Hide the alert.
              keepHidden = true;
            }
          }

          // Close button handler.
          $close_button.click(function (e) {
            e.preventDefault();

            // Hide the alert.
            $alert.addClass('au-page-alerts--hidden');

            // Save this node id in the cookies so we don't show it again.
            if (cookies.indexOf(nid) === -1) {
              cookies.push(nid);
              setCookie('health.alerts', cookies, 100);
            }
          });
        }

        // Show the alert if needed.
        if (!keepHidden) {
          $alert.removeClass('au-page-alerts--hidden');
        }
      });

    }
  }

})(jQuery, Drupal);


/**
 * Get a cookie.
 *
 * @param name
 * @returns {*}
 */
function getCookie(name) {
  var nameEQ = name + "=";
  var ca = document.cookie.split(';');
  for (var i = 0; i < ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) === ' ') c = c.substring(1, c.length);
    if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
  }
  return null;
}

/**
 * Set a cookie.
 *
 * @param name
 * @param value
 * @param years
 */
function setCookie(name, value, years) {
  var expires = "";
  if (years) {
    var date = new Date();
    date.setTime(date.getTime() + (years * 365 * 24 * 60 * 60 * 1000));
    expires = "; expires=" + date.toUTCString();
  }
  document.cookie = name + "=" + (value || "") + expires + "; path=/";
}
