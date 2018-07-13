(function ($, Drupal, window, document) {

  'use strict';

  Drupal.health = Drupal.health || {};
  Drupal.health.mobileNav = {};

  Drupal.behaviors.healthMobileNav = {
    attach: function (context, settings) {

      // Main menu navigation toggle.
      $('.mobile-toggle.mobile-toggle--main-menu', context).healthAccordion({
        speed: 500,
        beforeOpen: function() {
          Drupal.health.mobileNav.enableOverlay();
          Drupal.health.mobileNav.handleNavTabbing('search');
          Drupal.health.mobileNav.toggleText($(this)[0].this, 'menu');
        },
        beforeClose: function() {
          Drupal.health.mobileNav.disableOverlay();
          Drupal.health.mobileNav.toggleText($(this)[0].this, 'menu');
        },
        afterClose: function() {
          Drupal.health.mobileNav.disableOverlay(true);

        }
      });

      // Global search toggle.
      $('.mobile-toggle.mobile-toggle--search', context).healthAccordion({
        speed: 300,
        beforeOpen: function() {
          Drupal.health.mobileNav.enableOverlay();
          Drupal.health.mobileNav.handleNavTabbing('main-menu');
          Drupal.health.mobileNav.toggleText($(this)[0].this, 'search');
        },
        beforeClose: function() {
          Drupal.health.mobileNav.disableOverlay();
          Drupal.health.mobileNav.toggleText($(this)[0].this, 'search');
        },
        afterClose: function() {
          Drupal.health.mobileNav.disableOverlay(true);
        }
      });

      // Clicking outside the active site nav should close the nav.
      $('.nav-overlay', context).click(function () {
        // Deactivate nav if it is currently active.
        if ($('.mobile-toggle.mobile-toggle--main-menu', context).hasClass('au-accordion--open')) {
          $('.mobile-toggle.mobile-toggle--main-menu', context).trigger('click');
        }
        // Deactivate search if it is currently active.
        if ($('.mobile-toggle.mobile-toggle--search', context).hasClass('au-accordion--open')) {
          $('.mobile-toggle.mobile-toggle--search', context).trigger('click');
        }
      });

      $('.filter__mobile-title', context).click(function (e) {
        $(this).toggleClass('expanded');
        $('.block-facetapi', context).toggleClass('facetshow');
      });

      $('.filter-topics-by-letter__mobile-title', context).click(function (e) {
        $(this).toggleClass('expanded');
        $('.filter-topics-by-letter', context).toggleClass('facetshow');
      });

      // We are outputting 2 search forms, one for desktop and one for mobile.
      // It uses the same ID, which causes an accessibility issue.
      // So update the ID of the mobile one so it is different.
      $('.region-navigation #search-api-page-search-form').attr('id', 'search-api-page-search-form-mobile');

      // Show the desktop search bar.
      $('.region-header #block-search-api-page-default-search').removeClass('au-accordion--closed');
      
      /**
       * Enable the overlay.
       */
      Drupal.health.mobileNav.enableOverlay = function() {
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
      Drupal.health.mobileNav.disableOverlay = function(transition) {
        if (!$('.mobile-toggle--search', context).hasClass('au-accordion--open') && !$('.mobile-toggle--main-menu', context).hasClass('au-accordion--open')) {
          if (transition === true) {
            $('.nav-overlay', context).removeClass('transition');
          } else {
            $('.nav-overlay', context).removeClass('active');
          }
        }
      };

      /**
       * Swap text from Open to Close.
       * @param $element
       *   The element to change the text.
       * @param text
       *   The name of the button, eg search, menu etc to suffix to Open/Close
       */
      Drupal.health.mobileNav.toggleText = function ($element, text) {
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
      Drupal.health.mobileNav.handleNavTabbing = function(otherTab) {
        if ($('.mobile-toggle.mobile-toggle--' + otherTab, context).hasClass('au-accordion--open')) {
          $('.mobile-toggle.mobile-toggle--' + otherTab, context)
            .trigger('click');
        }
      };
    }
  };


})(jQuery, Drupal, this, this.document);