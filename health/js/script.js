/**
 * @file
 * A JavaScript file for the theme.
 *
 * In order for this JavaScript to be loaded on pages, see the instructions in
 * the README.txt next to this file.
 */

// JavaScript should be made compatible with libraries other than jQuery by
// wrapping it with an "anonymous closure". See:
// - https://drupal.org/node/1446420
// - http://www.adequatelygood.com/2010/3/JavaScript-Module-Pattern-In-Depth
(function ($, Drupal, window, document) {

  'use strict';

  // To understand behaviors, see https://drupal.org/node/756722#behaviors
  Drupal.behaviors.healthMatchHeight = {
    attach: function (context, settings) {

      // Match the related topics height
      $('.region-content-bottom .pane-related-content', context).matchHeight();
      $('.region-content-bottom .pane-title', context).matchHeight();

      // // Make the image left find the parent card to match image height
      $('.views-row .uikit-card-compact', context).matchHeight({byRow: false});
      $('.panel-panel .uikit-card-compact', context).matchHeight({byRow: false});

      // Match the categories height
      $('.view-categories .views-row .uikit-card', context).matchHeight();
      $('.view-categories .views-row .uikit-card h3', context).matchHeight();

      // Home page highlighted
      $('.region-highlighted .uikit-card .bean-title', context).matchHeight({byRow: false});
      $('.region-highlighted .uikit-card', context).matchHeight();
      
    }
  };

  Drupal.behaviors.healthCloseHealthAlert = {
    attach: function (context, settings) {

      // Hide the alert when the close button is pressed.
      $('#close-health-alert', context).click(function(e){
        e.preventDefault();
        $('body').toggleClass('health-alert-inactive');
      });
      
    }
  };

  // Flash up an environment indicator for PaaS.
  Drupal.behaviors.displayEnvironment = {
    attach: function (context, settings) {
      if ($('.environment', context).length > 0) {
        $('.environment').show().delay(1000).fadeOut(500);
      }
    }
  };

  // Responsive tables.
  Drupal.behaviors.responsiveTables = {
    attach: function (context, settings) {
      // Find any tables.
      // Add a div around them with with 'responsive-table-wrapper' class.
      $('table', context).wrap('<div class="responsive-table-wrapper"></div>');
    }
  };

})(jQuery, Drupal, this, this.document);
