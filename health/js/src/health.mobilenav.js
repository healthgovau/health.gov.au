(function ($, Drupal, window, document) {

  'use strict';

  Drupal.health = Drupal.health || {};
  Drupal.health.mobileNav = {};

  Drupal.behaviors.healthMobileNav = {
    attach: function (context, settings) {

      // Main menu navigation toggle.
      $('.au-main-nav__mobile-toggle--main-menu', context).click(function() {
        $(this).toggleClass('active');
        $('#block-superfish-1').toggleClass('active');
        if ($('.au-main-nav__mobile-toggle--search').hasClass('active')) {
          $('.au-main-nav__mobile-toggle--search').toggleClass('active');
          $('#block-search-api-page-default-search--2').toggleClass('active');
        } else {
          $('.au-main-nav__overlay', context).toggleClass('active');
        }
      });

      // Global search toggle.
      $('.au-main-nav__mobile-toggle--search', context).click(function() {
        $(this).toggleClass('active');
        $('#block-search-api-page-default-search--2').toggleClass('active');

        if ($('.au-main-nav__mobile-toggle--main-menu').hasClass('active')) {
          $('.au-main-nav__mobile-toggle--main-menu').toggleClass('active');
          $('#block-superfish-1').toggleClass('active');
        } else {
          $('.au-main-nav__overlay', context).toggleClass('active');
        }

      });

      // Clicking outside the active site nav should close the nav.
      $('.au-main-nav__overlay', context).click(function () {
        // Deactivate nav if it is currently active.
        if ($('.au-main-nav__mobile-toggle--main-menu', context).hasClass('active')) {
          $('.au-main-nav__mobile-toggle--main-menu', context).trigger('click');
        }
        // Deactivate search if it is currently active.
        if ($('.au-main-nav__mobile-toggle--search', context).hasClass('active')) {
          $('.au-main-nav__mobile-toggle--search', context).trigger('click');
        }
      });

      // Filter toggles.
      $('.health-filter__title', context).click(function (e) {
        $(this).toggleClass('expanded');
        $('.block-facetapi', context).toggleClass('facetshow');
      });

      $('.region-navigation #search-api-page-search-form-default-search--2').addClass('col-xs-12');
      
    }
  };


})(jQuery, Drupal, this, this.document);
