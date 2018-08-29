(function ($, Drupal, window, document, undefined) {

  Drupal.behaviors.videoPlay = {
    attach: function(context, settings) {

      //YouTube embed on click
      $(".field-name-field-image-featured", context).on("click", ".au-video__preview__link", function(e) {
        e.preventDefault();
        var videoid = $(this).attr("data-youtubeid");
        // Create an iFrame with autoplay set to true
        var iframe_url = "https://www.youtube.com/embed/" + videoid + "?autoplay=1&autohide=2&border=0&wmode=opaque&rel=0&html5=1&fs=1";
        if ($(this).data('params')) iframe_url += '&' + $(this).data('params');
        // The height and width of the iFrame should be the same as parent
        var iframe = $('<iframe/>', { 'title': 'YouTube video player', 'id': 'youtube-iframe', 'class': 'au-responsive-media-vid__item', 'allowfullscreen': 'allowfullscreen', 'frameborder': '0', 'aria-live': 'assertive', 'src': iframe_url });
        // Replace the YouTube thumbnail with YouTube HTML5 Player
        $('.au-video__preview__length', context).hide();
        $(this).replaceWith(iframe);
        $("iframe", context).wrap( '<div class="au-responsive-media-vid au-responsive-media-vid--16x9"></div>' );
      });
    }
  };

})(jQuery, Drupal, this, this.document);
