(function ($, Drupal, window, document) {

  'use strict';

  /**
   * Footnotes and references.
   */
  Drupal.behaviors.footnotes = {
    attach: function (context, settings) {
      // Find all the footnote and reference links.
      $('.reference__link a, .footnote__link a')
        .each(function() {
          // Add a title attribute to each link based on the full footnote/reference.
          // This is used for tippy js.
          $(this).attr('title', $($(this).attr('aria-describedby')).html());
        })
        .click(function(e) {
          // Don't scroll down if we are on mobile.
          if ($(window).width() < 768) {
            e.preventDefault();
          }
          // Add a highlight class.
          $('a[name="' + e.target.hash.substring(1) + '"]').parent('li').addClass('highlight');
          window.setTimeout(function () {
            $('a[name="' + e.target.hash.substring(1) + '"]').parent('li').removeClass('highlight');
          }, 2000);
        });
    }
  };

})(jQuery, Drupal, this, this.document);
