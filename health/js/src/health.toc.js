(function ($, Drupal, window, document) {

  'use strict';

  // Modified version of smooth scrolling from toc_filter module.
  Drupal.tocScrollToOnClick = function() {
    // Make sure links still has hash.
    if (!this.hash || this.hash == '#') {
      return true;
    }

    // Make sure the href is pointing to an anchor link on this page.
    var href = this.href.replace(/#[^#]*$/, '');
    var url = window.location.toString();
    if (href && url.indexOf(href) === -1) {
      return true;
    }

    // Find hash target.
    var $a = $('h2[id=' + this.hash.substring(1) + ']');

    // Make hash target is on the current page.
    if (!$a.length) {
      return true;
    }

    // Scroll to hash target
    $('html, body').animate({scrollTop: $a.offset().top}, 'medium');
    return false;
  };

  // Generating table of contents.
  Drupal.behaviors.toc = {
    attach: function (context, settings) {
      $('.uikit-inpage-nav-links__heading', context).text(Drupal.settings.toc.title);
      $('.region-content', context).anchorific({
        navigation: '.toc',
        headers: 'h2',
      });

      // Generate id's for all headings that don't have them so we can deep link.
      $('h2,h3,h4,h5,h6').each(function () {
        if ($(this).attr('id') === undefined) {
          var id = $(this).text().replace(/[^\w\s]/gi, '')
            .replace(/\s+/g, '-')
            .toLowerCase();
          $(this).attr('id', id);
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

  // Triggering smooth scrolling.
  Drupal.behaviors.tocSmoothScroll = {
    attach: function (context) {
      // Only map <a href="#..."> links
      $('.toc a[href*="#"]', context).click(Drupal.tocScrollToOnClick);
    }
  };

})(jQuery, Drupal, this, this.document);
