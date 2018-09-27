(function($, Drupal) {

  Drupal.behaviors.health_adminimal_media_manager_selector = {
    attach: function (context, settings) {
      var $media = $('.field-name-field-related-image');
      $media.each(function() {
        var $mediaItem = $(this);

        var $data = $mediaItem.find('.form-autocomplete');

        // Browse button.
        var $browse = $('<a href="" class="button">Search</a>');
        $browse.click(function(e) {
          e.preventDefault();

          // Create iframe #sad
          var $iframe = $('<div id="media-library" data-field="' + $data.attr('id') + '"><iframe src="/admin/content/media/images"></iframe></div>');
          $mediaItem.append($iframe);

          // Put it in a dialog.
          $iframe.dialog({
            title: 'Media Library',
            modal: true,
            width: '90%',
            height: 1000,
            close: function() {
              $(this).dialog("destroy");
            }
          });

          // Listen to iframe for the event that the user has selected something.
          window.document.addEventListener('media-library-selected', function(e) {
            $('#' + $iframe.attr('data-field')).val(e.detail.id);
            $iframe.dialog('close');
            $iframe.remove();
            $data.trigger('change');
          }, {capture: false, once: true});

        });
        $(this).once('media-library-browse').append($browse);

        // Edit button.
        var $edit = $('<a href="#" target="_blank" class="button">Edit</a>');
        $edit.click(function(e) {
          var nid = $data.val().substring($data.val().indexOf('(')+1);
          nid = nid.replace(')', '');
          $edit.attr('href', '/node/' + nid + '/edit');
        });
        $(this).once('media-library-edit').append($edit);

        // Hide the edit button when there are no values in the field.
        $data.change(function() {
          if ($(this).val() === '') {
            $edit.hide();
          } else {
            $edit.show();
          }
        });
        $data.trigger('change');
      });
    }
  };

})(jQuery, Drupal);
