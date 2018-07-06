(function ($, Drupal, window, document) {

  'use strict';

  /**
   * Add a highlight class when a footnote or reference link is clicked.
   */
  Drupal.behaviors.footnotes = {
    attach: function (context, settings) {
      // Find all the footnote and reference links.
      $('.reference__link, .footnote__link')
        .each(function() {
          // Add a title attribute to each link based on the full footnote/reference.
          // This is used for tippy js.
          $(this).attr('title', $($(this).attr('aria-describedby')).html());
        });
    }
  };

})(jQuery, Drupal, this, this.document);
