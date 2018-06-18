(function ($, Drupal, window, document) {

  'use strict';

  /**
   * Add a highlight class when a footnote or reference link is clicked.
   */
  Drupal.behaviors.footnotes = {
    attach: function (context, settings) {
      $('.reference__link a, .footnote__link a')
        .each(function() {
          $(this).attr('title', $($(this).attr('aria-describedby')).text());
        })
        .click(function(e) {
          // Don't scroll down if we are on mobile.
          if ($(window).width() < 768) {
            e.preventDefault();
          }
          $('a[name="' + e.target.hash.substring(1) + '"]').parent('li').addClass('highlight');
          window.setTimeout(function () {
            $('a[name="' + e.target.hash.substring(1) + '"]').parent('li').removeClass('highlight');
          }, 2000);
        });
    }
  };

})(jQuery, Drupal, this, this.document);
