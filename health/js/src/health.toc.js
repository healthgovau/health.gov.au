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
      $('.au-inpage-nav-links__heading', context).text(Drupal.settings.toc.title);
      $('.region-content', context).anchorific({
        navigation: '.au-inpage-nav-links',
        headers: 'h2',
        anchorText: false,
        spy: false
      });
    }
  };

  // Triggering smooth scrolling.
  Drupal.behaviors.tocSmoothScroll = {
    attach: function (context) {
      // Only map <a href="#..."> links
      $('.au-inpage-nav-links a[href*="#"]', context).click(Drupal.tocScrollToOnClick);
    }
  };

})(jQuery, Drupal, this, this.document);
