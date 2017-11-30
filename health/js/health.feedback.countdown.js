(function ($, Drupal, window, document) {

  'use strict';

  // To understand behaviors, see https://drupal.org/node/756722#behaviors
  Drupal.behaviors.countdown = {
    attach: function (context, settings) {
      new Vue({
        el: '#feedback-textarea',
        data: {
          message: ''
        }
      });
      new Vue({
        el: '#improvement-textarea',
        data: {
          message: ''
        }
      });
    }
  };
})(jQuery, Drupal, this, this.document);
