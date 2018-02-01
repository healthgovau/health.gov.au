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

  // Enable fontawesome pseudo element.
  Drupal.behaviors.fontAwesome = {
    attach: function (context, settings) {
      window.FontAwesomeConfig = {
        searchPseudoElements: true
      };
    }
  };

  Drupal.behaviors.healthMatchHeight = {
    attach: function (context, settings) {

      // Match the related topics height
      $('.region-content-bottom .pane-related-content', context).matchHeight();
      $('.region-content-bottom .pane-title', context).matchHeight();

      // Match the categories height
      $('.view-categories .views-row .uikit-card', context).matchHeight();
      $('.view-categories .views-row .uikit-card h3', context).matchHeight();

      // Home page highlighted
      $('.region-highlighted .uikit-card .bean-title', context).matchHeight({byRow: false});
      $('.region-highlighted .uikit-card', context).matchHeight();

      // Listing page selector cards.
      $('.selector-card', context).matchHeight();

      // Title section match height.
      $('.page-title .col', context).matchHeight();

      // Lifestage on topic page
      $('.page-node-1021 .field-name-field-related-term', context).matchHeight();

      // Front page task accelerator.
      $('.front .paragraphs-item-para-block.para-container.block__count--5 .bean-block-content a', context).matchHeight();

      // Front page feedback band.
      $('.feedback-col', context).matchHeight();

      // Statistics
      $('.group-statistic .field-name-field-statistic-value-text', context).matchHeight();
      $('.group-trend', context).matchHeight();

      // About us boxes
      $('#block-bean-homepage-healthcare-system .group-left, #block-bean-homepage-healthcare-system .group-right', context).matchHeight();

      // Homepage - In our portfolio
      $('#block-bean-ageing-and-aged-care .bean-image-and-text, #block-bean-homepage-portfolio-sport .bean-image-and-text', context).matchHeight();

      // Immunisation more on immunisation band
      $('.paragraphs-item-para-block.block-id__more-services-card .bean-image-and-text a', context).matchHeight();
    }
  };

  Drupal.behaviors.healthMobileMenu = {
    attach: function (context, settings) {

      // Main menu navigation toggle.
      $('.mobile-toggle.mobile-toggle--main-menu', context).click(function (e) {
        Drupal.toggleText($(this), 'menu');
        UIKIT.accordion.Toggle( $(this)[0], 400,
          {
            onOpen: function() {
              Drupal.handleNavTabbing('search');
              Drupal.enableOverlay();
            },
            afterOpen: function() {},
            onClose: function() {
              Drupal.disableOverlay(false);
            },
            afterClose: function() {
              Drupal.disableOverlay(true);
            }
          }
        );
      });

      // Global search toggle.
      $('.mobile-toggle.mobile-toggle--search', context).click(function (e) {
        Drupal.toggleText($(this), 'search');
        UIKIT.accordion.Toggle( $(this)[0], 200,
          {
            onOpen: function() {
              Drupal.handleNavTabbing('main-menu');
              Drupal.enableOverlay();
            },
            afterOpen: function() {},
            onClose: function() {
              Drupal.disableOverlay(false);
            },
            afterClose: function() {
              Drupal.disableOverlay(true);
            }
          }
        );
      });

      // Clicking outside the active site nav should close the nav.
      $('.nav-overlay', context).click(function() {
        // Deactivate nav if it is currently active.
        if ($('.mobile-toggle.mobile-toggle--main-menu', context).hasClass('uikit-accordion--open')) {
          $('.mobile-toggle.mobile-toggle--main-menu').trigger('click');
        }
        // Deactivate search if it is currently active.
        if ($('.mobile-toggle.mobile-toggle--search', context).hasClass('uikit-accordion--open')) {
          $('.mobile-toggle.mobile-toggle--search').trigger('click');
        }
      });

      // Local navigation
      $('.mobile-toggle.mobile-toggle__local-nav a', context).click(function (e) {
        e.preventDefault();
        $("#block-menu-block-2", context).toggleClass('mobilemenu-active');
        $(".mobile-toggle.mobile-toggle__local-nav").toggleClass('mobilemenu-active');
      });

      $('.filter__mobile-title', context).click(function (e) {
        $(this).toggleClass('expanded');
        $('.block-facetapi', context).toggleClass('facetshow');
      });

      $('.filter-topics-by-letter__mobile-title', context).click(function (e) {
        $(this).toggleClass('expanded');
        $('.filter-topics-by-letter', context).toggleClass('facetshow');
      });
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

  // Responsive tables.
  Drupal.behaviors.responsiveTables = {
    attach: function (context, settings) {
      // Find any tables.
      // Add a div around them with with 'responsive-table-wrapper' class.
      $('table', context).wrap('<div class="responsive-table-wrapper"></div>');
    }
  };

  // Add classes and elements to external links.
  Drupal.behaviors.externalLinks = {
    attach: function (context, settings) {
      // Add external links.
      Drupal.health.externalLinks();
      // Add class to any li surrounding an external link.
      $('a[rel=external]', context).parent('li').addClass('external-link');
    }
  };

  // Use singular when there is only 1 result in the view list.
  Drupal.behaviors.singularResult = {
    attach: function (context, settings) {
      var total = $('.view-header span', context).text();
      if (total === '1') {
        $('.view-header span', context).after(' result');
      }
      else {
        $('.view-header span', context).after(' results');
      }
    }
  };

  /**
   * Enable the overlay.
   */
  Drupal.enableOverlay = function() {
    $('.nav-overlay').addClass('transition').addClass('active');
  };

  /**
   * Disable the overlay only if no tabs are currently active.
   * Supports switching between tabs without the overlay disappearing.
   *
   * @param bool complete
   *   Is this the start of end of the animation / transition.
   *
   */
  Drupal.disableOverlay = function(complete) {
    if (complete === false) {
      $('.nav-overlay').removeClass('active');
    } else if (!$('.mobile-toggle--search').hasClass('uikit-accordion--open') && !$('.mobile-toggle--main-menu').hasClass('uikit-accordion--open')) {
      $('.nav-overlay').removeClass('transition');
    }
  };

  /**
   * Swap text from Open to Close.
   * @param $element
   *   The element to change the text.
   * @param text
   *   The name of the button, eg search, menu etc to suffix to Open/Close
   */
  Drupal.toggleText = function ($element, text) {
    // Update text.
    if ($element.find('span').text().trim() === 'Open ' + text) {
      $element.find('span').text('Close ' + text);
    } else {
      $element.find('span').text('Open ' + text);
    }
  };

  /**
   * Handle when a tab is already active and other tab is clicked.
   * @param otherTab
   *   The name of the other tab, eg search, main-menu
   */
  Drupal.handleNavTabbing = function(otherTab) {
    if ($('.mobile-toggle.mobile-toggle--' + otherTab).hasClass('uikit-accordion--open')) {
      $('.mobile-toggle.mobile-toggle--' + otherTab)
        .trigger('click');
    }
  };

})(jQuery, Drupal, this, this.document);
