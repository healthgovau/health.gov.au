(function($, Drupal) {

  Drupal.behaviors.health_adminimal_media_manager_library = {
    attach: function (context, settings) {
      // Layout the items nicely.
      $('.view-content').isotope({
        itemSelector: 'li',
        masonry: {
          gutter: 10
        }
      });

      // Click handler for each result.
      $('.view-content a').click(function(e) {
        e.preventDefault();
        // Send the click event to the parent.
        window.parent.document.dispatchEvent(
          new CustomEvent('media-library-selected', {detail: {id: $(this).attr('data-id')}})
        );
      });
    }
  };

})(jQuery, Drupal);
