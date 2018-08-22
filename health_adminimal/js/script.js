(function($, Drupal) {

  // Store the collapsed state of the fieldsets, so they can be restored later.
  var collapsed = [];

  Drupal.behaviors.health_adminimal = {
    attach: function(context) {

      // Do a fake resize to get the toolbar top padding to fix itself.
      $(window).trigger('resize');

      // Collapse and expand buttons for field components paragraph blocks  .
      // Create buttons.
      var collapse = '<a href="#" class="collapse-all-link">Collapse all</a> | ';
      var expand = '<a href="#" class="expand-all-link">Expand all</a>';

      var links = '<div>' + collapse + expand + '</div>';

      // Add to dom.
      $('.field-name-field-components .tabledrag-toggle-weight-wrapper').first()
        .once('collapse-all-link').append(' | ' + collapse)
        .once('expand-all-link').append(expand);

      // Collapse handler.
      $('.collapse-all-link').once('collapse-all-link-handler').click(function (e) {
        e.preventDefault();
        $('.field-name-field-components fieldset:not(.filter-wrapper)').not('.collapsed').each(function() {
          $(this).find('.fieldset-legend a').first().trigger('click');
        });
      });
      // Expand handler.
      $('.expand-all-link').once('expand-all-link-handler').click(function (e) {
        e.preventDefault();
        $('.field-name-field-components fieldset:not(.filter-wrapper)').filter('.collapsed').each(function() {
          $(this).find('.fieldset-legend a').first().trigger('click');
        });
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
      legendSummary('.paragraphs-item-type-content-callout', 'Callout', 'textarea');

      // Bands.
      legendSummary('.paragraphs-item-type-para-view', 'Band - Listing', '.field-name-field-title input');
      legendSummary('.paragraphs-item-type-featured-videos', 'Band - Featured video', 'select:not(".filter-list")');
      legendSummary('.paragraphs-item-type-featured-resource', 'Band - Featured resource', 'select:not(".filter-list")');
      legendSummary('.paragraphs-item-type-featured-news-events', 'Band - Featured news', 'select:not(".filter-list")');
      legendSummary('.paragraphs-item-type-para-block', 'Band - Block', '.field-name-field-title input, .field-name-field-para-block-id input');
      legendSummary('.paragraphs-item-type-two-columns', 'Two columns', '.field-name-field-pbundle-title input');
      legendSummary('.paragraphs-item-type-para-link', 'Band - Link', 'input[type="text"]');
      legendSummary('.paragraphs-item-type-para-links', 'Band - Links', '.field-name-field-pbundle-title input');
      legendSummary('.paragraphs-item-type-para-taxonomies', 'Band - Taxonomies', '.field-name-field-pbundle-title input');
      legendSummary('.paragraphs-item-type-para-taxonomy', 'Band - Term', '.field-name-field-related-term input');
      legendSummary('.paragraphs-item-type-para-statistics', 'Band - Statistics', '.field-name-field-title input');
      legendSummary('.paragraphs-item-type-para-contact', 'Band - Contact', 'select:not(".filter-list")');
      legendSummary('.paragraphs-item-type-para-feature-content', 'Band - Featured content', 'input[type="text"]');
      legendSummary('.paragraphs-item-type-para-conditions-and-diseases', 'Band - Conditions and diseases', 'input[type="text"]');
      legendSummary('.paragraphs-item-type-band-static-content', 'Band - Static', 'textarea');


      // Publications
      legendSummary('.paragraphs-item-type-documents', 'Part', '.field-name-field-resource-file-title input');
      legendSummary('.paragraphs-item-type-document', 'File', '.media-item');

      // Long docs
      legendSummary('.paragraphs-item-type-recommendation', 'Recommendation', 'textarea');
      legendSummary('.paragraphs-item-type-content-table', 'Table', '.field-name-field-title input');
      legendSummary('.paragraphs-item-type-glossary-term', 'Term', 'input');
      legendSummary('.paragraphs-item-type-figure', 'Figure', '.field-name-field-title input');

      // If collapsed already has saved states in it, restore those states.
      if (collapsed.length > 0) {
        $('.field-name-field-components > div > div > div > .field-multiple-table > tbody > tr').each(function (index) {
          if (collapsed[index] === false) {
            $(this).find('> td > fieldset').removeClass('collapsed');
          }
        });
      }

      // When the add more button is clicked, save the current state of the collapsed sections.
      $('.paragraphs-add-more-submit').once('add-another-paragraph').mousedown(function() {
        collapsed = [];
        $('.field-name-field-components > div > div > div > .field-multiple-table > tbody > tr').each(function (index) {
          if ($(this).find('td:nth-of-type(2) fieldset').first().hasClass('collapsed')) {
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
              summary = createSummary($('<div>' + $(this).find(inputSelector).val() + '</div>').find('h2,h3,h4,h5,h6,p').first().text(), initialText);
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
                  var summary = createSummary($('<div>' + evt2.editor.getData() + '</div>').find('h2,h3,h4,h5,h6,p').first().text(), initialText);
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
        initialText = '<span class="block-title">' + initialText + '</span>';
        if (text == '') {
          return initialText;
        }
        if (text.length > 100) {
          return initialText + ': ' + text.substr(0, 75) + '…';
        } else {
          return initialText + ': ' + text;
        }
      }

      /**
       * Provide a human readable version of file types based on their mime type.
       *
       * @param type
       * @returns {*}
       */
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

      /**
       * Prevent users from choosing a text format.
       *
       * @param format
       * @param fields
       */
      function lockTextFormat(format, fields) {
        for(var i=0;i<fields.length;i++) {
          $(fields[i]).val(format);
          $(fields[i]).parent().hide();
        }
      }

      // Apply chosen using the new version of chosen.
      $('.chosen-enable').chosen({width: 400});

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
      $('span.form-required').text('*');

      // Lock down the text format authors can use.

      // HTML table.
      lockTextFormat('table', [
        '.paragraphs-item-type-content-table .field-name-field-body .filter-list'
      ]);

      // Simple.
      lockTextFormat('simple', [
        '.paragraphs-item-type-content-table .field-name-field-table-source .filter-list',
        '.paragraphs-item-type-content-figure .field-name-field-table-source .filter-list',
        '.paragraphs-item-type-references .field-name-field-book-references .filter-list',
        '.paragraphs-item-type-footnotes .field-name-field-book-footnotes .filter-list'
      ]);

      // Simple rich text
      lockTextFormat('simple_rich_text', [
        '.node-video-form .field-name-field-description .filter-list',
        '.node-publication-form .field-name-field-description .filter-list'
      ]);
    }
  };

  // Add required label to nmm ID field for orderable publication.
  Drupal.behaviors.health_adminimal_orderable_publication = {
    attach: function (context, settings) {
      $('.field-name-field-publication-orderable input', context).on('click', function() {
        if ($(this).is(':checked')) {
          if (!$('.form-item-field-publication-nmm-id-und-0-value .form-required', context).length) {
            $('.form-item-field-publication-nmm-id-und-0-value label', context).append('<span class="form-required" title="This field is required.">*</span>');
          }
        }
        else {
          $('.form-item-field-publication-nmm-id-und-0-value .form-required', context).remove();
        }
      });
    }
  };

  Drupal.behaviors.health_adminimal_dates = {
    attach: function (context) {

      // Checkbox.
      var $enabled = $('.field-name-field-enable-manual-date-editing input', context);
      // Dates to enable/disable.
      var dates = ['.field-name-field-date-updated', '.field-name-field-date-published'];

      // Set the enabled/disabled state on load.
      if ($enabled.is( ":checked" ) === false) {
        for(var i=0; i<dates.length; i++) {
          $(dates[i], context).hide();
        }
      }

      // When the checkbox changes, toggle enabled/disabled.
      $enabled.on('change', function() {
        for(var i=0; i<dates.length; i++) {
          $(dates[i], context).toggle();
        }
      });

    }
  };

})(jQuery, Drupal);
