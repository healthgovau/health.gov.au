(function ($, Drupal, window, document) {

  'use strict';

  // Create anchors and scroll if needed.
  Drupal.behaviors.anchors = {
    attach: function (context, settings) {

      // Generate id's for all headings that don't have them so we can deep link.
      $('body:not(.page-views)').find('.region-content').find('h2,h3,h4,h5,h6').each(function () {
        if ($(this).attr('id') === undefined) {
          var id = $(this).text().replace(/[^\w\s]/gi, '')
            .replace(/\s+/g, '-')
            .toLowerCase();
          $(this).attr('id', id);
        }
        if ($('body').hasClass('logged-in')) {
          $(this).append('<span class="anchor-helper">#'+$(this).attr('id')+'</span>');
        }
      });
      // Once it has finished adding anchors, check the url anchor fragment and scroll to that heading if needed.
      if (window.location.hash) {
        // Find hash target.
        var $a = $(window.location.hash);
        // Make hash target is on the current page.
        if (!$a.length) {
          return true;
        }
        // Scroll to hash target
        $('html, body').animate({scrollTop: $a.offset().top}, 'medium');
        $a.focus();
      }
    }
  };

})(jQuery, Drupal, this, this.document);

