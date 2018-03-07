(function ($, Drupal, window, document) {

  'use strict';

  Drupal.behaviors.healthMobileMenu = {
    attach: function (context, settings) {

      // Main menu navigation toggle.
      $('.mobile-toggle.mobile-toggle--main-menu', context).click(function (e) {
        Drupal.toggleText($(this), 'menu');
        UIKIT.accordion.Toggle($(this)[0], 400,
          {
            onOpen: function () {
              Drupal.handleNavTabbing('search');
              Drupal.enableOverlay();
            },
            afterOpen: function () {
            },
            onClose: function () {
              Drupal.disableOverlay(false);
            },
            afterClose: function () {
              Drupal.disableOverlay(true);
            }
          }
        );
      });

      // Global search toggle.
      $('.mobile-toggle.mobile-toggle--search', context).click(function (e) {
        Drupal.toggleText($(this), 'search');
        UIKIT.accordion.Toggle($(this)[0], 200,
          {
            onOpen: function () {
              Drupal.handleNavTabbing('main-menu');
              Drupal.enableOverlay();
            },
            afterOpen: function () {
            },
            onClose: function () {
              Drupal.disableOverlay(false);
            },
            afterClose: function () {
              Drupal.disableOverlay(true);
            }
          }
        );
      });

      // Clicking outside the active site nav should close the nav.
      $('.nav-overlay', context).click(function () {
        // Deactivate nav if it is currently active.
        if ($('.mobile-toggle.mobile-toggle--main-menu', context).hasClass('uikit-accordion--open')) {
          $('.mobile-toggle.mobile-toggle--main-menu', context).trigger('click');
        }
        // Deactivate search if it is currently active.
        if ($('.mobile-toggle.mobile-toggle--search', context).hasClass('uikit-accordion--open')) {
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

      /**
       * Enable the overlay.
       */
      Drupal.enableOverlay = function() {
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
      Drupal.disableOverlay = function(complete) {
        if (complete === false) {
          $('.nav-overlay', context).removeClass('active');
        } else if (!$('.mobile-toggle--search', context).hasClass('uikit-accordion--open') && !$('.mobile-toggle--main-menu', context).hasClass('uikit-accordion--open')) {
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
        if ($('.mobile-toggle.mobile-toggle--' + otherTab, context).hasClass('uikit-accordion--open')) {
          $('.mobile-toggle.mobile-toggle--' + otherTab, context)
            .trigger('click');
        }
      };
    }
  };


})(jQuery, Drupal, this, this.document);