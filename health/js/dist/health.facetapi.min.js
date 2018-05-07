(function ($, Drupal, window, document) {

  'use strict';

  /**
   * Replace the original Drupal.facetapi.makeCheckbox javascript with our own custom one.
   * Replace an unclick link with a checked checkbox.
   */
  if (Drupal.facetapi) {
    Drupal.facetapi.makeCheckbox = function () {
      var $link = $(this),
        active = $link.hasClass('facetapi-active'),
        $li = $(this).parent('li');

      if (!active && !$link.hasClass('facetapi-inactive')) {
        // Not a facet link.
        return;
      }

      // Derive an ID and label for the checkbox based on the associated link.
      // The label is required for accessibility, but it duplicates information
      // in the link itself, so it should only be shown to screen reader users.
      var id = this.id + '--checkbox',
        label = $('<label class="uikit-control-input" for="' + id + '"><div class="uikit-control-input__text"></div></label>'),
        checkbox = $('<input type="checkbox" class="facetapi-checkbox uikit-control-input__input" id="' + id + '" />'),
        // Get the href of the link that is this DOM object.
        href = $link.attr('href'),
        redirect = new Drupal.facetapi.Redirect(href);

      checkbox.click(function (e) {
        Drupal.facetapi.disableFacet($link.parents('ul.facetapi-facetapi-checkbox-links'));
        redirect.gotoHref();
      });

      label.prepend(checkbox);
      $li.prepend(label);

      if (active) {
        $link.remove();
        label.find('div').text($li.text());
      } else {
        $link.find('.element-invisible').remove();
        label.find('div ').text($link.text());
        $link.remove();
      }

      $li.contents().filter(function () {
        return this.nodeType == 3;
      }).first().replaceWith("");

      if (active) {
        checkbox.attr('checked', true);
      }
    }
  }

})(jQuery, Drupal, this, this.document);
