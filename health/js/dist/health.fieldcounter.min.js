(function ($, Drupal, window, document) {
  Drupal.behaviors.fieldCounters = {
    attach: function (context, settings) {

      /**
       * Add a field counter to a field.
       *
       * @param string key
       *   The id of the wrapper around a form element, with a {{message}} placeholder
       */
      Drupal.health = Drupal.health || {};
      Drupal.health.fieldCounter = function (key) {
        var field = new Vue({
          el: key,
          data: {message: ''},
          mounted: function () {
            // Remove the required attribute.
            var invalid = $(this.$el).find('*:invalid');

            if (invalid.length > 0) {
              invalid.removeAttr('required');
              invalid.blur(function () {
                $(this).attr('required', true);
              });
            }
          }
        });
      };
    }
  };
})(jQuery, Drupal, this, this.document);