(function ($, Drupal, window, document) {

  'use strict';

  Drupal.health = Drupal.health || {};
  Drupal.health.mobilenav = {};

  Drupal.behaviors.healthMobileMenu = {
    attach: function (context, settings) {

      // Main menu navigation toggle.
      $('.mobile-toggle.mobile-toggle--main-menu', context).click(function (e) {
        Drupal.health.mobilenav.toggleText($(this), 'menu');
        $(this).healthAccordion({
            speed: 500,
            onOpen: function() {
              //Drupal.health.mobilenav.enableOverlay();
              //Drupal.health.mobilenav.handleNavTabbing('search');
            },
            onClose: function() {
              //Drupal.health.mobilenav.disableOverlay();
            }
        });
      });

      // Global search toggle.
      $('.mobile-toggle.mobile-toggle--search', context).click(function (e) {
        Drupal.health.mobilenav.toggleText($(this), 'search');
        $(this).healthAccordion({
          speed: 300,
          onOpen: function() {
            //Drupal.health.mobilenav.enableOverlay();
            //Drupal.health.mobilenav.handleNavTabbing('main-menu');
          },
          onClose: function() {
            //Drupal.health.mobilenav.disableOverlay();
          }
        });
      });

      // Clicking outside the active site nav should close the nav.
      $('.nav-overlay', context).click(function () {
        // Deactivate nav if it is currently active.
        if ($('.mobile-toggle.mobile-toggle--main-menu', context).hasClass('health-accordion--open')) {
          $('.mobile-toggle.mobile-toggle--main-menu', context).trigger('click');
        }
        // Deactivate search if it is currently active.
        if ($('.mobile-toggle.mobile-toggle--search', context).hasClass('health-accordion--open')) {
          $('.mobile-toggle.mobile-toggle--search', context).trigger('click');
        }
      });

      // Local navigation
      $('.mobile-toggle.mobile-toggle__local-nav a', context).click(function (e) {
        e.preventDefault();
        $("#block-menu-block-2", context).toggleClass('mobilemenu-active');
        $(".mobile-toggle.mobile-toggle__local-nav", context).toggleClass('mobilemenu-active');
      });

      $('.filter__mobile-title', context).click(function (e) {
        $(this).toggleClass('expanded');
        $('.block-facetapi', context).toggleClass('facetshow');
      });

      $('.filter-topics-by-letter__mobile-title', context).click(function (e) {
        $(this).toggleClass('expanded');
        $('.filter-topics-by-letter', context).toggleClass('facetshow');
      });

      // Because we are outputting 2 search forms, one for desktop and one for mobile.
      // It uses the same ID, which causes an accessibility issue.
      // So update the ID of the mobile one so it is different.
      $('.region-navigation #search-api-page-search-form').attr('id', 'search-api-page-search-form-mobile');

      /**
       * Enable the overlay.
       */
      Drupal.health.mobilenav.enableOverlay = function() {
        $('.nav-overlay', context).addClass('transition').addClass('active');
      };

      /**
       * Disable the overlay only if no tabs are currently active.
       * Supports switching between tabs without the overlay disappearing.
       *
       * @param bool complete
       *   Is this the start of end of the animation / transition.
       *
       */
      Drupal.health.mobilenav.disableOverlay = function(complete) {
        if (complete === false) {
          $('.nav-overlay', context).removeClass('active');
        } else if (!$('.mobile-toggle--search', context).hasClass('health-accordion--open') && !$('.mobile-toggle--main-menu', context).hasClass('health-accordion--open')) {
          $('.nav-overlay', context).removeClass('transition');
        }
      };

      /**
       * Swap text from Open to Close.
       * @param $element
       *   The element to change the text.
       * @param text
       *   The name of the button, eg search, menu etc to suffix to Open/Close
       */
      Drupal.health.mobilenav.toggleText = function ($element, text) {
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
      Drupal.health.mobilenav.handleNavTabbing = function(otherTab) {
        if ($('.mobile-toggle.mobile-toggle--' + otherTab, context).hasClass('health-accordion--open')) {
          $('.mobile-toggle.mobile-toggle--' + otherTab, context)
            .trigger('click');
        }
      };
    }
  };


})(jQuery, Drupal, this, this.document);