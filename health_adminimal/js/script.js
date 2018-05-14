(function($, Drupal) {

  // Store the collapsed state of the fieldsets, so they can be restored later.
  var collapsed = [];

  Drupal.behaviors.health_adminimal = {
    attach: function(context) {

      // Collapse and expand buttons for field components paragraph blocks  .
      // Create buttons.
      var collapse = '<a href="#" class="collapse-all-link">Collapse all</a> | ';
      var expand = '<a href="#" class="expand-all-link">Expand all</a>';

      var links = '<div>' + collapse + expand + '</div>';

      // Add to dom.
      $('.field-name-field-components .tabledrag-toggle-weight-wrapper').first().once('collapse-all-link').append(' | ' + collapse);
      $('.field-name-field-components .tabledrag-toggle-weight-wrapper').first().once('expand-all-link').append(expand);

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
      // References
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

      // Content
      legendSummary('.paragraphs-item-type-para-content-text', 'Text', 'textarea');
      legendSummary('.paragraphs-item-type-para-content-image', 'Image', '.media-item');
      legendSummary('.paragraphs-item-type-para-content-external-link', 'External link', 'input');

      // Landing pages
      legendSummary('.paragraphs-item-type-para-view', 'Listing', '.field-name-field-title input');
      legendSummary('.paragraphs-item-type-featured-videos', 'Featured video', 'select:not(".filter-list")');
      legendSummary('.paragraphs-item-type-featured-resource', 'Featured resource', 'select:not(".filter-list")');
      legendSummary('.paragraphs-item-type-featured-news-events', 'Featured news', 'select:not(".filter-list")');
      legendSummary('.paragraphs-item-type-para-block', 'Block', '.field-name-field-title input, .field-name-field-para-block-id input');
      legendSummary('.paragraphs-item-type-two-columns', 'Two columns', '.field-name-field-pbundle-title input');
      legendSummary('.paragraphs-item-type-para-link', 'Link', 'input[type="text"]');
      legendSummary('.paragraphs-item-type-para-links', 'Links', '.field-name-field-pbundle-title input');
      legendSummary('.paragraphs-item-type-para-taxonomies', 'Taxonomies', '.field-name-field-pbundle-title input');
      legendSummary('.paragraphs-item-type-para-taxonomy', 'Taxonomy', '.field-name-field-related-term input');
      legendSummary('.paragraphs-item-type-para-statistics', 'Statistics', '.field-name-field-title input');

      //paragraphs-item-type-para-contact
      //field-name-field-related-term

      // Publications
      legendSummary('.paragraphs-item-type-documents', 'Part', '.field-name-field-resource-file-title input');
      legendSummary('.paragraphs-item-type-document', 'File', '.media-item');
      //
      //

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
          var summary = '';
          if ($(this).find(inputSelector).length) {

            // Media browser preview
            if (inputSelector === '.media-item') {

              // File
              if ($(this).find(inputSelector).hasClass('media-type__document')) {
                var label = $(this).find(inputSelector).find('a').text();
                label += convert_mime($(this).find(inputSelector).find('svg').attr('data-mime'));
                summary = createSummary(label, initialText);

              // Image
              } else if ($(this).find(inputSelector).hasClass('media-type__image')) {
                var alt = $(this).find(inputSelector).attr('title');
                if (alt === '') {
                  alt = $(this).find(inputSelector).find('img').attr('alt');
                }
                alt += ' (Image)';
                summary = createSummary(alt, initialText);
              }

            } else if (inputSelector.indexOf('select') !== -1) {
              var value = $(this).find(inputSelector).first().find(':selected').text();
              if (value === '- Select a value -' || value === '- None -') {
                value = ''
              }
              summary = createSummary(value, initialText);
            } else if (inputSelector.indexOf('textarea') !== -1) {
              summary = createSummary($($(this).find(inputSelector).val()).first().text(), initialText);
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
                  var summary = createSummary($(evt2.editor.getData()).first().text(), initialText);
                  $('#' + id).parents('fieldset').first().find('legend a').first().html(summary);
                });
              }
            });
          });
        } else {
          // If the input changes, update the summary.
          $(paraSelector + ' ' + inputSelector).once(initialText).blur(function () {
            var summary = '';
            if (inputSelector.indexOf('select') !== -1) {
              summary = createSummary($(this).find('option:selected').text(), initialText);
            } else {
              summary = createSummary($(this).val(), initialText);
            }
            $(this).parents(paraSelector).find('legend a').first().html(summary);
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
        initialText = '<strong>' + initialText + '</strong>';
        if (text == '') {
          return initialText;
        }
        if (text.length > 100) {
          return initialText + ': ' + text.replace(/(\(\d+\))/, '').substr(0, 100) + '…';
        } else {
          return initialText + ': ' + text.replace(/(\(\d+\))/, '');
        }
      }

      function convert_mime(type) {
        switch(type) {
          case 'application/pdf':
            return ' (PDF)';
          case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
          case 'application/msword':
            return ' (Word)';
          case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
            return ' (Excel)';
          case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
            return ' (Powerpoint)';
          case 'text/plain':
            return ' (Text)';
          case 'image/jpeg':
            return ' (JPG)';
          case 'image/png':
            return ' (PNG)';
          case 'image/gif':
            return ' (GIF)';
        }
        return '';
      }


      // Apply chosen using the new version of chosen.
      $('.chosen-enable').chosen();

      // Add some placeholder text.
      $('.form-item-title input').attr('placeholder', 'Enter a title');

      // Remove mandatory from empty labels.
      // We would do this in the template override, but forms_accessible is such a terrible module that the override doesn't work
      // https://www.drupal.org/project/accessible_forms/issues/2971863
      $('label').each(function() {
        if ($(this).text().trim() === '(mandatory)') {
          $(this).hide();
        }
      });

    }
  };

})(jQuery, Drupal);
