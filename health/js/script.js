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
    }
  };

  Drupal.behaviors.healthMobileMenu = {
    attach: function (context, settings) {

      // Hide the alert when the close button is pressed.
      $('.mobile-toggle', context).click(function (e) {
        e.preventDefault();
        $(this).next('div').toggleClass('mobilemenu-active');
        $(this).toggleClass('mobilemenu-active');
      });

      $('.mobile-nav-toggle', context).click(function (e) {
        e.preventDefault();
        $(this).next('div').toggleClass('mobilemenu-active');
        $(this).toggleClass('mobilemenu-active');
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

  // Remove required = true until form element has lost focus.
  Drupal.behaviors.formValidate = {
    attach: function (context, settings) {

      var reqItems = $(".node-form *:invalid");

      reqItems.each(function () {
        var element = $(this);
        // Only apply to elements without a maxlength.
        // Elements with maxlength are handled by vuejs.
        if (element.attr('maxlength') == -1) {
          element.removeAttr('required');

          element.blur(function () {
            element.attr("required", "true");
          });
        }
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
      $('a[rel=external]').parent('li').addClass('external-link');
    }
  };

  /*
   * Replace all SVG images with inline SVG
   */
  Drupal.behaviors.inlineSVG = {
    attach: function (context, settings) {
      $('.para-container.block-id__topic-tile img').each(function(){
        var $img = $(this);
        var imgID = $img.attr('id');
        var imgClass = $img.attr('class');
        var imgURL = $img.attr('src');

        $.get(imgURL, function(data) {
          // Get the SVG tag, ignore the rest
          var $svg = $(data).find('svg');

          // Add replaced image's ID to the new SVG
          if(typeof imgID !== 'undefined') {
            $svg = $svg.attr('id', imgID);
          }
          // Add replaced image's classes to the new SVG
          if(typeof imgClass !== 'undefined') {
            $svg = $svg.attr('class', imgClass+' replaced-svg');
          }

          // Remove any invalid XML tags as per http://validator.w3.org
          $svg = $svg.removeAttr('xmlns:a');

          // Check if the viewport is set, if the viewport is not set the SVG wont't scale.
          if(!$svg.attr('viewBox') && $svg.attr('height') && $svg.attr('width')) {
            $svg.attr('viewBox', '0 0 ' + $svg.attr('height') + ' ' + $svg.attr('width'));
          }

          // Replace image with new SVG
          $img.replaceWith($svg);

        }, 'xml');

      });
    }
  };


})(jQuery, Drupal, this, this.document);
