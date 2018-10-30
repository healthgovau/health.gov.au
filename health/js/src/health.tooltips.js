(function ($, Drupal, window, document) {

  'use strict';

  Drupal.behaviors.tooltips = {
    attach: function (context, settings) {
      if (typeof tippy !== 'undefined') {
        tippy('.region-content span[title], .region-content abbr', {
          arrow: false,
          theme: 'health-tooltip',
          performance: true,
          allowTitleHTML: true,
          interactive: true,
          animateFill: false,
        });

        // Share menu.
        tippy('#health-toolbar__share', {
          arrow: false,
          theme: 'health-tooltip',
          performance: true,
          allowTitleHTML: true,
          interactive: true,
          animateFill: false,
          html: document.getElementById("health-share-menu"),
          trigger: 'click',
          appendTo: $("#health-toolbar__share").parent('li')[0]
        });
      }
    }
  }

})(jQuery, Drupal, this, this.document);
