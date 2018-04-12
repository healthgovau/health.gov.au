(function ($) {

  'use strict';

  $.fn.healthAccordion = function( options ) {
    var settings = $.extend({
      speed: 500,
      onOpen: null,
      onClose: null,
      this: this
    }, options );

    if (settings.onOpen && !$('#' + settings.this.attr('aria-controls')).hasClass("health-accordion--open")) {
      settings.onOpen();
    }
    settings.this.toggleClass("health-accordion--open");
    $('#' + settings.this.attr('aria-controls')).toggleClass("health-accordion--open");
    $('#' + settings.this.attr('aria-controls')).animate({
      height: "toggle"
    }, settings.speed, function () {
      if (settings.onClose && !$('#' + settings.this.attr('aria-controls')).hasClass("health-accordion--open")) {
        settings.onClose();
      }
    });

  };

})(jQuery);
