(function($, Drupal) {

  // Store the collapsed state of the fieldsets, so they can be restored later.
  var collapsed = [];

  Drupal.behaviors.health_adminimal = {
    attach: function(context) {

      // Collapse and expand buttons for field components paragraph blocks  .
      if (!$('body').hasClass('node-type-health-topic')) {
        // Create buttons.
        var collapse = '<a href="#" class="collapse-all-link">Collapse all</a> | ';
        var expand = '<a href="#" class="expand-all-link">Expand all</a>';

        var links = '<div>' + collapse + expand + '</div>';

        // Add to dom.
        $('.field-name-field-components .tabledrag-toggle-weight-wrapper').first().once('collapse-all-link').append(' | ' + collapse);
        $('.field-name-field-components .tabledrag-toggle-weight-wrapper').first().once('expand-all-link').append(expand);
        $('.field-name-field-components .paragraphs-add-more-submit').once('links').after(links);

        // Collapse handler.
        $('.collapse-all-link').once('collapse-all-link-handler').click(function (e) {
          e.preventDefault();
          $('.field-name-field-components fieldset:not(.collapsed) .fieldset-legend a').trigger('click');
        });
        // Expand handler.
        $('.expand-all-link').once('expand-all-link-handler').click(function (e) {
          e.preventDefault();
          $('.field-name-field-components fieldset.collapsed:not(.filter-wrapper) .fieldset-legend a').trigger('click');
        });

        // Add a summary to the legend of what content is in a block.
        legendSummary('.paragraphs-item-type-para-reference-video', 'Video', 'input[type="text"]');
        legendSummary('.paragraphs-item-type-para-reference-publication', 'Publication', 'input[type="text"]');
        legendSummary('.paragraphs-item-type-para-reference-service', 'Service', 'input[type="text"]');
        legendSummary('.paragraphs-item-type-para-reference-campaign', 'Campaign', 'input[type="text"]');
        legendSummary('.paragraphs-item-type-para-reference-conditions-and-di', 'Conditions and diseases', 'input[type="text"]');
        legendSummary('.paragraphs-item-type-para-reference-contact', 'Contact', 'input[type="text"]');
        legendSummary('.paragraphs-item-type-para-reference-event', 'Event', 'input[type="text"]');
        legendSummary('.paragraphs-item-type-para-reference-program-and-initi', 'Programs and initiatives', 'input[type="text"]');
        legendSummary('.paragraphs-item-type-para-reference-news', 'News', 'input[type="text"]');
        legendSummary('.paragraphs-item-type-para-reference-health-alert', 'Health alert', 'input[type="text"]');
        legendSummary('.paragraphs-item-type-reference-statistic', 'Statistic', 'input[type="text"]');

        legendSummary('.paragraphs-item-type-para-content-text', 'Text', 'textarea');
        legendSummary('.paragraphs-item-type-para-content-image', 'Image', 'img');
        legendSummary('.paragraphs-item-type-para-content-external-link', 'External link', 'input');

        // If collapsed already has saved states in it, restore those states.
        if (collapsed.length > 0) {
          $('.field-name-field-components .field-multiple-table tbody tr').each(function (index) {
            if (collapsed[index] == true) {
              $(this).find('td > fieldset').addClass('collapsed');
            }
          });
        }

        // When the add more button is clicked, save the current state of the collapsed sections.
        $('.paragraphs-add-more-submit').once('add-another-paragraph').mousedown(function() {
          collapsed = [];
          $('.field-name-field-components .field-multiple-table tbody tr').each(function (index) {
            if ($(this).find('td fieldset').first().hasClass('collapsed')) {
              collapsed[index] = true;
            } else {
              collapsed[index] = false;
            }
          });
        });
      }

      /**
       * Generate a summary of the content in a block and put it in the legend.
       * Triggers when content is changed.
       *
       * @param paraSelector
       *   The selector for the paragraph element.
       * @param initialText
       *   The default text for the legend.
       * @param inputSelector
       *    The selector for the element to watch for on blur.
       */
      function legendSummary(paraSelector, initialText, inputSelector) {

        // Update the summary on load.
        $(paraSelector).each(function () {
          var inputElement = $(this).find(inputSelector), summary = '';
          if ($(this).find(inputSelector).length) {
            if (inputSelector == 'img') {
              var alt = $(this).find(inputSelector).attr('alt');
              if (alt == '') {
                alt = $(this).find(inputSelector).parents('.media-item').attr('title');
              }
              summary = createSummary(alt, initialText);
            } else {
              summary = createSummary($(this).find(inputSelector).val(), initialText);
            }
            $(this).find('legend a').first().html(summary);
          }
        });

        // Handlers for when the content changes.
        if (inputSelector.indexOf('textarea') !== -1 && $(inputSelector).hasClass('wysiwyg')) {
          // CKEditor.
          CKEDITOR.on('instanceReady', function (evt) {
            $(paraSelector + ' ' + inputSelector).each(function() {
              var id = $(this).attr('id');
              if (evt.editor.name == id) {
                evt.editor.on('blur', function (evt2) {
                  var summary = createSummary(evt2.editor.getData(), initialText);
                  $('#' + id).parents('fieldset').first().find('legend a').first().text(summary);
                });
              }
            });
          });
        } else {
          // If the input changes, update the summary.
          $(paraSelector + ' ' + inputSelector).once(initialText).blur(function () {
            var inputElement = $(this), summary = '';
            if (inputSelector == 'img') {
              summary = createSummary($(this).attr('alt'), initialText);
            } else {
              summary = createSummary($(this).val(), initialText);
            }
            $(this).parents(paraSelector).find('legend a').text(summary);
          });
        }
      }

      /**
       * Create the summary text.
       *
       * @param text
       *   The value of the content in the paragraph.
       * @param initialText
       *   The default text for the legend.
       * @returns string
       */
      function createSummary(text, initialText) {
        if (text == '') {
          return initialText;
        }
        if (text.length > 50) {
          return initialText + ': ' + strip(text).replace(/(\(\d+\))/, '').substr(0, 50) + '…';
        } else {
          return initialText + ': ' + strip(text).replace(/(\(\d+\))/, '');
        }
      }

      /**
       * Remove HTML from a string.
       *
       * @param html
       * @returns {string|string}
       */
      function strip(html) {
        var tmp = document.createElement("DIV");
        tmp.innerHTML = html;
        return tmp.textContent || tmp.innerText || "";
      }

      // Apply chosen using the new version of chosen.
      $('.chosen-enable').chosen();

      // Make sure users can only use the HTML Table format for table paragraphs.
      var formats = $('.paragraphs-item-type-content-table .field-name-field-body .filter-list');
      formats.val('html_table'); // Switch to that format for new tables.
      formats.hide();
    }
  };

})(jQuery, Drupal);
