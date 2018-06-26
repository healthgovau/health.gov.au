(function ($, Drupal, window, document) {

  'use strict';

  Drupal.behaviors.tooltips = {
    attach: function (context, settings) {
      if (typeof tippy !== 'undefined') {
        tippy('.region-content a[title]', {
          arrow: true,
          theme: 'health-tooltip',
          performance: true,
          allowTitleHTML: true,
          interactive: true
        });
      }
    }
  }

})(jQuery, Drupal, this, this.document);
