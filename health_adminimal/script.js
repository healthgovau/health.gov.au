(function($, Drupal) {

  Drupal.behaviors.health_adminimal = {
    attach: function(context) {

      // Content owner - prevent users from selecting anything except a branch (third level).
      $('#edit-field-content-owner-und option').each(function() {
        if ($(this).text().substring(0,2) !== '--') {
          $(this).attr('disabled', 'disabled');
        }
        if ($(this).val() !== '_none') {
          $(this).text($(this).text().replace(/-/g, '- '));
          $(this).text($(this).text().replace(/- -/g, '--'));
        }
      });

      // Provide a collapse all button for paragraph blocks.
      if (!$('body').hasClass('node-type-health-topic')) {
        var collapse = '<a href="#" class="collapse-all-link">Collapse all</a> | ';
        var expand = '<a href="#" class="expand-all-link">Expand all</a>';

        var links = '<div>' + collapse + expand + '</div>';

        $('.tabledrag-toggle-weight-wrapper').first().once('collapse-all-link').append(' | ' + collapse);
        $('.tabledrag-toggle-weight-wrapper').first().once('expand-all-link').append(expand);
        $('.paragraphs-add-more-submit').once('links').after(links);

        $('.collapse-all-link').click(function (e) {
          e.preventDefault();
          $('.field-name-field-components fieldset:not(.collapsed) .fieldset-legend a').trigger('click');
          if ($(".group-standard-page").length) {
            $('html, body').animate({
              scrollTop: $(".group-standard-page").offset().top - 100
            }, 500);
          }
        });
        $('.expand-all-link').click(function (e) {
          e.preventDefault();
          $('.field-name-field-components fieldset.collapsed:not(.filter-wrapper) .fieldset-legend a').trigger('click');
          if ($(".group-standard-page").length) {
            $('html, body').animate({
              scrollTop: $(".group-standard-page").offset().top - 100
            }, 500);
          }
        });

        legendSummary('.paragraphs-item-type-para-reference-video', 'Video', 'input');
        legendSummary('.paragraphs-item-type-para-reference-publication', 'Publication', 'input');
        legendSummary('.paragraphs-item-type-para-reference-service', 'Service', 'input');
        legendSummary('.paragraphs-item-type-para-reference-campaign', 'Campaign', 'input');
        legendSummary('.paragraphs-item-type-para-reference-conditions-and-di', 'Conditions and diseases', 'input');
        legendSummary('.paragraphs-item-type-para-reference-contact', 'Contact', 'input');
        legendSummary('.paragraphs-item-type-para-reference-event', 'Event', 'input');
        legendSummary('.paragraphs-item-type-para-reference-program-and-initi', 'Programs and initiatives', 'input');
        legendSummary('.paragraphs-item-type-para-reference-news', 'News', 'input');
        legendSummary('.paragraphs-item-type-para-reference-health-alert', 'Health alert', 'input');
        legendSummary('.paragraphs-item-type-reference-statistic', 'Statistic', 'input');

        legendSummary('.paragraphs-item-type-para-content-text', 'Text', 'textarea');
        legendSummary('.paragraphs-item-type-para-content-image', 'Image', 'img');
        legendSummary('.paragraphs-item-type-para-content-external-link', 'External link', 'input');
      }


      function legendSummary(paraSelector, initialText, input) {
        $(paraSelector).each(function() {
          var inputElement = $(this).find(input), summary = '';
          if (input == 'img') {
            summary = createSummary($(this).find(input).attr('alt'), initialText);
          } else {
            summary = createSummary($(this).find(input).val(), initialText);
          }
          $(this).find('legend a').first().text(summary);
        });


        /*if (input == 'textarea') {
          $(paraSelector + ' ' + input).once('textarea-ckeditor').each(function() {
            var $this = $(this);
            CKEDITOR.on( 'instanceReady', function( evt ) {
              if (evt.editor.name == $(paraSelector + ' ' + input).attr('id')) {
                evt.editor.on('blur', function (evt) {
                  var summary = createSummary(evt.editor.getData(), initialText);
                  $this.parents(paraSelector).find('legend a').first().text(summary);
                });
              }
            });
          });
        } else {*/
          $(paraSelector + ' ' + input).blur(function () {
            var inputElement = $(this), summary = '';
            if (input == 'img') {
              summary = createSummary($(this).attr('alt'), initialText);
            } else {
              summary = createSummary($(this).val(), initialText);
            }
            $(this).parents(paraSelector).find('legend a').text(summary);
          });
        //}
      }

      function createSummary(text, initialText) {
        if (text.length > 30) {
          return initialText + ' (<span title=" ' + text + '">' + strip(text).substr(0,30) + '…</span>)';
        } else {
          return initialText + ' (' + text + ')';
        }
      }

      function strip(html)
      {
        var tmp = document.createElement("DIV");
        tmp.innerHTML = html;
        return tmp.textContent || tmp.innerText || "";
      }

    }
  };

})(jQuery, Drupal);
