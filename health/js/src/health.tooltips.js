(function ($, Drupal, window, document) {

  'use strict';

  Drupal.behaviors.tooltips = {
    attach: function (context, settings) {
      if (typeof tippy !== 'undefined') {
        tippy('.region-content span[title], .region-content abbr', {
          arrow: true,
          theme: 'health-tooltip',
          performance: true
        });
      }
    }
  }

})(jQuery, Drupal, this, this.document);
