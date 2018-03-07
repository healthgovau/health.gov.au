(function ($, Drupal, window, document) {

  'use strict';

  // To understand behaviors, see https://drupal.org/node/756722#behaviors
  Drupal.behaviors.imageAnchor = {
    attach: function (context, settings) {
      $(".link-wrapper", context).click(function() {
        $('html, body').animate({
          scrollTop: $(".paragraphs-items-field-publication-files", context).offset().top
        }, 2000);
      });
    }
  };
})(jQuery, Drupal, this, this.document);
