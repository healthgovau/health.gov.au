// Globally store these variables for use later by another script.
//var title, alt, version;

var newRevision;

(function($, Drupal) {

  Drupal.behaviors.health_adminimal_file_new_revision = {
    attach: function (context) {

      // Go through each media field.
      $('.field-widget-media-generic').each(function() {

        if ($(this).find('.media-item').length) {
          var type = getMediaType($(this).find('.media-item').attr("class"));
          if (type === 'image') {
            $(this).find('.button.edit').text('Update alt text');
          } else {
            $(this).find('.button.edit').hide();
          }

          // Add a new revision button.
          if ($(this).find('.image-new-revision').length === 0) {
            var button = '<a href="#" class="image-new-revision button">New revision</a>';
            $(this).find('.media-widget').append(button);
          }
        }

        // If the image widget has reloaded and we have variables, click the browse button.
        if (typeof newRevision != 'undefined') {
          $(this).find('.button.browse').trigger('click');
        }
      });

      // Handler for the new revision button
      $('.field-widget-media-generic .image-new-revision').click(function(e) {
        e.preventDefault();

        // Store the name, alt and version of the current file.
        var $preview = $(this).parents('.media-widget');
        newRevision = {};
        newRevision.filename = $preview.find('.media-filename').html();
        newRevision.type = getMediaType($preview.find('.media-item').attr("class"));
        newRevision.alt = $preview.find('.media-thumbnail__image img').attr('alt');

        // Trigger a click of the remove button.
        $preview.find('input.remove').trigger('mousedown').hide();

        // Hide the new revision button and the update alt button.
        $(this).hide();
        $preview.find('.button.edit').hide();
      });

      // When closing the browse window, reset newRevision so it doesn't trigger the browse button again.
      $('.ui-dialog-titlebar-close').click(function() {
        newRevision = undefined;
      });

    }
  };

  /**
   * Deduce the media type based on classes.
   *
   * @param classes
   * @returns {null}
   */
  function getMediaType(classes) {
    classes = classes.split(/\s+/);
    for (var i=0;i<classes.length;i++) {
      if (classes[i].indexOf('media-type__') !== -1) {
        return classes[i].replace('media-type__', '');
      }
    }
    return null;
  }

})(jQuery, Drupal);