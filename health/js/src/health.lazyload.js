(function ($, Drupal, window, document) {

  'use strict';

  Drupal.behaviors.lazyLoadImages = {
    attach: function (context, settings) {
      if (typeof LazyLoad !== 'undefined') {
        var myLazyLoad = new LazyLoad({
          callback_load: function (el) {
            // Remove all the space reserving class and styles.
            $(el).parents('.image-wrapper')
              .removeClass('image-loading')
              .removeClass('image-wrapper')
              .css('padding-bottom', '')
              .find('.image')
              .removeClass('image');
          }
        });
      }

      // If the user tries to print, then try to load all the images so printing works :)
      // https://developer.mozilla.org/en-US/docs/Web/API/WindowEventHandlers/onbeforeprint



      // Webkit browsers.
      if(typeof window.webkitConvertPointFromNodeToPage === 'function') {
        var mediaQueryList = window.matchMedia('print');
        mediaQueryList.addListener('change', function (mql) {
          if (mql.matches) {
            // Onbeforeprint equivalent for webkit browsers (Safari etc)
          }
        });
      } else {
        //window.onbeforeprint
        $(window).on('onbeforeprint', function() {
          console.log('onbeforeprint');
        });
      }
    }
  };

})(jQuery, Drupal, this, this.document);

