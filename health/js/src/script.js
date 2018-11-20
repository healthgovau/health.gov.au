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

      if (typeof $.fn.matchHeight !== 'undefined') {
        // Match the related topics height
        $('.region-content-bottom .pane-related-content', context).matchHeight();
        $('.region-content-bottom .pane-title', context).matchHeight();

        // Match the categories height
        $('.view-categories .views-row .au-card', context).matchHeight();
        $('.view-categories .views-row .au-card h3', context).matchHeight();

        // Home page highlighted
        $('.region-highlighted .au-card .bean-title', context).matchHeight({byRow: false});
        $('.region-highlighted .au-card', context).matchHeight();

        // Listing page selector cards.
        $('.selector-card', context).matchHeight();

        // Title section match height.
        $('.au-sub-header > .container > .row > div', context).matchHeight();

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

        // Vertical static text para cols
        $('.paragraphs-item-para-static-vertical-text .col-sm-7, .paragraphs-item-para-static-vertical-text .static-vertical-wrapper', context).matchHeight();

        // Immunisation more on immunisation band
        $('.paragraphs-item-para-block.block-id__more-services-card .bean-image-and-text a', context).matchHeight();

        // Front page feedback band.
        $('.view-mode-view_selector .bean-block-content a', context).matchHeight();

        // Bean block - solid colour card.
        $('.view-mode-solid_full_card .bean-block-content a', context).matchHeight();

        // au-card.
        $('.au-card-list--matchheight .au-card', context).matchHeight();

        // Featured contact.
        $('.view-mode-hotline_bar .hotline', context).matchHeight();

        // Inline statistics
        $('.paragraphs-item-reference-statistic .stat__card').matchHeight();

        // Home page - Resources
        $('.paragraphs-view-latest_rsources .views-row').matchHeight();
      }
    }
  };

  Drupal.behaviors.healthCloseHealthAlert = {
    attach: function (context, settings) {

      // Hide the alert when the close button is pressed.
      $('#close-health-alert', context).click(function(e){
        e.preventDefault();
        $('body', context).toggleClass('health-alert-inactive');
      });

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
        $('.view__top span', context).after(' result');
      }
      else {
        $('.view__top span', context).after(' results');
      }
    }
  };

  Drupal.behaviors.healthPrint = {
    attach: function (context, settings) {
      $('#health-toolbar__print').click(function (e) {
        e.preventDefault();
        window.print();
      });
    }
  };

  Drupal.behaviors.healthShare = {
    attach: function (context, settings) {
      $("#health-toolbar__share").click(function (e) {
        e.preventDefault();
      });
    }
  };

  Drupal.behaviors.healthContacts = {
    attach: function (context, settings) {
      // Hide the left column if it is empty (no image)
      if (!$(".node-contact.view-mode-full .group-left img").length) {
        $(".node-contact.view-mode-full .group-left").removeClass('col-sm-3');
        $(".node-contact.view-mode-full .group-right").removeClass('col-sm-8').removeClass('col-sm-offset-1').addClass("col-sm-12");
      }
    }
  };

  Drupal.behaviors.healthMobileSearch = {
    attach: function (context, settings) {
      var resizeTimer; // Set resizeTimer to empty so it resets on page load

      function resizeFunction() {
        if ($(window).width() > 769) {
          $('.au-search--global-desktop').insertAfter('#block-menu-menu-sub-menu');
        } else {
          $('.au-search--global-desktop').insertAfter('#main-nav-default');
        }
      };

      // On resize, run the function and reset the timeout
      $(window).resize(function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(resizeFunction, 250);
      });

      resizeFunction();

      $('.au-main-nav__toggle--search').click(function() {
        $('.au-search--global-desktop').toggle().find('.au-search__form__input').focus();
        $(this).find('i').toggleClass('fa-search').toggleClass('fa-times');
      });
    }
  };

})(jQuery, Drupal, this, this.document);
