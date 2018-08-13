(function ($, Drupal, window, document) {

  'use strict';

  /**
   * Footnotes and references.
   */
  Drupal.behaviors.footnotes = {
    attach: function (context, settings) {
      // Find all the footnote and reference links.
      $('.au-references__link, .au-footnotes__link')
        .each(function() {
          // Add a title attribute to each link based on the full footnote/reference.
          // This is used for tippy js.
          $(this).attr('title', $($(this).attr('aria-describedby')).html());
        });
    }
  };

})(jQuery, Drupal, this, this.document);
