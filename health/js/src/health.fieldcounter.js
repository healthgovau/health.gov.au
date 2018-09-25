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
      Drupal.health.fieldCounter = function (key, length) {
        var $field = $(key + ' textarea');
        $field.keyup(function() {
          $(key).find('.form-element-length-counter').text(parseInt(length) - $field.val().length + ' characters remaining');
        });
      };
    }
  };
})(jQuery, Drupal, this, this.document);
