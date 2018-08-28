(function ($) {

  'use strict';

  $.fn.healthAccordion = function( options ) {
    var settings = $.extend({
      speed: 500,
      beforeOpen: null,
      afterOpen: null,
      beforeClose: null,
      afterClose: null,
      this: this
    }, options );

    settings.this.once('healthAccordion').click(function(e) {

      e.preventDefault();

      var $target = $('#' + settings.this.attr('aria-controls'));

      settings.this.toggleClass("health-accordion--open").toggleClass("health-accordion--closed");

      if (settings.this.hasClass("health-accordion--open")) {
        if (settings.beforeOpen) {
          settings.beforeOpen();
        }
        setAriaRoles(settings.this[0], $target[0], false);
      } else if (!settings.this.hasClass("health-accordion--open")) {
        if (settings.beforeClose) {
          settings.beforeClose();
        }
        setAriaRoles(settings.this[0], $target[0], true);
      }

      $target.animate({
        height: 'toggle'
      },
        settings.speed,
        function () {
          $target.toggleClass("health-accordion--open").toggleClass("health-accordion--closed");

          if (settings.afterOpen && settings.this.hasClass("health-accordion--open")) {
            settings.afterOpen();
          } else if (settings.afterClose && !settings.this.hasClass("health-accordion--open")) {
            settings.afterClose();
          }
        }
      );

      /**
       * PRIVATE
       * Set the correct Aria roles for given element on the accordion title and body
       *
       * @param  {object} element  - The DOM element we want to set attributes for
       * @param  {object} target   - The DOM element we want to set attributes for
       * @param  {string} state    - The DOM element we want to set attributes for
       */
      function setAriaRoles( element, target, closed ) {

        if( closed === true ) {
          target.setAttribute( 'aria-hidden', true );
          element.setAttribute( 'aria-expanded', false );
          element.setAttribute( 'aria-selected', false );
        }
        else {
          target.setAttribute( 'aria-hidden', false );
          element.setAttribute( 'aria-expanded', true );
          element.setAttribute( 'aria-selected', true );
        }
      }

    });

  };

  // Setup any accordions that have the health_accordion class.
  $('.health-accordion:not(.health-accordion--skip-auto) .health-accordion__title').each(function() { $(this).healthAccordion(); });

})(jQuery);
