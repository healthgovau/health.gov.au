(function($, Drupal) {

  Drupal.behaviors.health_adminimal = {
    attach: function(context) {

      // Content owner - prevent users from selecting anything except a branch (third level).
      $('#edit-field-content-owner-und option').each(function() {
        if ($(this).text().substring(0,2) !== '--') {
          $(this).attr('disabled', 'disabled');
        }
        if ($(this).val() !== '_none') {
          $(this).text($(this).text().replace(/-/g, '- '));
          $(this).text($(this).text().replace(/- -/g, '--'));
        }
      });
    }
  };

})(jQuery, Drupal);
