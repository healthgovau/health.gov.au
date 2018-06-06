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

      // Front page feedback band.
      $('.view-mode-view_selector .bean-block-content a', context).matchHeight();

      // Bean block - solid colour card.
      $('.view-mode-solid_full_card .bean-block-content a', context).matchHeight();
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
        $('.view-header span', context).after(' result');
      }
      else {
        $('.view-header span', context).after(' results');
      }
    }
  };

  Drupal.behaviors.tooltips = {
    attach: function (context, settings) {
      if (typeof tippy !== 'undefined') {
        tippy('.reference__link a, .footnote__link a, .abbreviation, .definition', {
          arrow: true,
          theme: 'health-tooltip',
          performance: true
        });
      }
    }
  }

})(jQuery, Drupal, this, this.document);
