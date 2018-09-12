(function($, Drupal) {

  Drupal.behaviors.health_adminimal_file_populate_revision_info = {
    attach: function (context) {

      // If we are on the add new file form.
      var $addForm = $('#file-entity-add-upload');
      if ($addForm.length) {

        if (typeof parent.newRevision != 'undefined') {

          // First step, remove the option to select from library.
          if ($addForm.find('#edit-upload-upload').length) {
            $('#branding').hide();
          }

          // Second step, name, version and file.
          if ($addForm.find('#edit-filename').length) {

            // Disable the save button until ajax is done.
            $addForm.find('#edit-submit').hide();

            // Populate the fields.

            // Title.
            $('#edit-filename').val(parent.newRevision.filename)
              .attr('disabled', 'disabled')
              .parents('.form-item')
              .addClass('form-disabled');

            // Alt text.
            $('#edit-field-file-image-alt-text-und-0-value').val(parent.newRevision.alt);

            // Version
            $('#edit-field-file-version-und-0-value')
              .attr('disabled', 'disabled')
              .hide()
              .parents('.form-item')
              .addClass('form-disabled')
              .append('<div class="ajax-progress ajax-progress-throbber">Getting latest version...<div class="throbber">&nbsp;</div></div>'); // Add throbber.

            // Find the latest version of this file.
            $.ajax({
              url: window.location.origin + "/admin/content/files/version/" + parent.newRevision.type + '/' + parent.newRevision.filename,
              type: 'GET',
              success: function (data) {
                var version = $('.field-content', data).text();
                if (version === '') {
                  version = '0';
                }
                version = parseInt(version) + 1;
                $('#edit-field-file-version-und-0-value')
                  .val(version)
                  .show()
                  .parents('.form-item')
                  .find('.ajax-progress').remove();
                $addForm.find('#edit-submit').show();
              },
              error: function (data) {
                $('#edit-field-file-version-und-0-value')
                  .val(1)
                  .show()
                  .parents('.form-item')
                  .find('.ajax-progress').remove();
                $addForm.find('#edit-submit').show();
              }
            });

            // Submit click handler.
            $addForm.find('#edit-submit').click(function () {
              // Remove disabled as drupal freaks out!
              $('.form-item').removeClass('form-disabled').find('input').attr('disabled', '');
              // Reset the variables so this doesn't trigger again.
              parent.newRevision = undefined;
            });
          }
        }
      }

      // If we are on the edit form.
      var $editForm = $('#file-entity-edit');
      if ($editForm.length) {
        // Hide fields we don't want authors to edit.
        $editForm.find('.form-item-files-replace-upload').hide();
        $editForm.find('.form-item-filename').hide();
        $editForm.find('.field-name-field-file-version').hide();
      }

    }
  };

})(jQuery, Drupal);