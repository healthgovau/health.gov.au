(function ($) {
  Drupal.health = Drupal.health || {};
  /**
   * Add rel=external to any external links.
   * Does not apply to links with images in them.
   * @param string - selector
   *    A jquery selector for the a to look for.
   */
  Drupal.health.externalLinks = function(selector) {
    selector = selector || 'a';
    $(selector).filter(function() {
      if (($(this).find('img').length === 0) && (!$(this).hasClass('js-ignoreext'))){ // Exclude links around images.
        return this.hostname && this.hostname !== location.hostname;
      }
      return false;
    }).attr('rel','external').append('<i class="fas fa-external-link-alt fa-xs"></i>');
  };
})(jQuery);