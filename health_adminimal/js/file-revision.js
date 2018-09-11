// Globally store these variables for use later by another script.
//var title, alt, version;

var newRevision;

(function($, Drupal) {

  Drupal.behaviors.health_adminimal_file_new_revision = {
    attach: function (context) {

      // Go through each media field.
      $('.field-widget-media-generic').each(function() {
        // Remove the edit button if present.00
        $(this).find('.button.edit').hide();

        // Add a new revision button if this field has a image in it.
        if ($(this).find('input.remove').length > 0) {
          var button = '<a href="" class="image-new-revision button edit">New revision</a>';
          $(this).find('.media-widget').append(button);
        }

        // If the image widget has reloaded and we have variables, click the browse button.
        var id = $(this).find('.media-widget').attr('id');
        if (typeof newRevision != 'undefined') {
          $(this).find('.button.browse').trigger('click');
        }
      });

      // Handler for the new revision button
      $('.field-widget-media-generic .image-new-revision').click(function(e) {
        e.preventDefault();

        // Store the name, alt and version of the current file.
        var $preview = $(this).parents('.media-widget');
        var id = $preview.attr('id');
        newRevision = {};
        newRevision.filename = $preview.find('.media-filename').html();
        var classes = $preview.find('.media-item').attr("class").split(/\s+/);
        for (var i=0;i<classes.length;i++) {
          if (classes[i].indexOf('media-type__') !== -1) {
            newRevision.type = classes[i].replace('media-type__', '');
          }
        }
        newRevision.alt = $preview.find('.media-thumbnail__image img').attr('alt');

        // Trigger a click of the remove button.
        $preview.find('input.remove').trigger('mousedown').hide();

        // Hide the new revision button.
        $(this).hide();
      });

      $('.ui-dialog-titlebar-close').click(function() {
        newRevision = undefined;
      });

    }
  };

})(jQuery, Drupal);