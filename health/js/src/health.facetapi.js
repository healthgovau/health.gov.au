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
        label = $('<label class="au-control-input au-control-input--block" for="' + id + '"><span class="au-control-input__text"></span></label>'),
        checkbox = $('<input type="checkbox" class="facetapi-checkbox au-control-input__input" id="' + id + '" />'),
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

        var text = $li.text();

        // If this checkbox doesn't have a number next to it in brackets, add the number of current results.
        if (/\(\d+\)/gm.exec($li.text()) === null) {
          var count = $('.view__summary__count');
          if (count.length > 0) {
            text += ' (' + $('.view__summary__count').text() + ')';
          }
        }

        label.find('span').text(text);

      } else {
        
        $link.find('.element-invisible').remove();

        // If this checkbox doesn't have a number next to it in brackets, add (0).
        if (/\(\d+\)/gm.exec($link.text()) === null) {
          $link.text($link.text() + ' (0)');
        }

        label.find('span ').text($link.text());
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

