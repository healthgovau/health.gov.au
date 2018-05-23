(function ($) {
  Drupal.health = Drupal.health || {};

  /**
   * Add a highlight class when a footnote or reference link is clicked.
   */
  Drupal.health.footnotes = function() {
    $('.reference__link a, .footnote__link a')
      .each(function() {
        $(this).attr('title', $($(this).attr('href')).parent('li,p').text());
      })
      .click(function(e) {
        e.preventDefault();
        $(e.target.hash).parent('li,p').addClass('highlight');
        window.setTimeout(function() {
          $(e.target.hash).parent('li,p').removeClass('highlight');
        }, 2000);
      });
  };
})(jQuery);