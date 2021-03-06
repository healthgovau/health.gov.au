(function ($) {

  Drupal.behaviors.healthDefinitions = {
    attach: function (context, settings) {
      if (settings.health.definitions) {
        var counter = 0;
        $('span.definition--candidate').each(function() {
          counter++;
          // Check the parent, we don't want to replace in all situations.
          if ($(this).parent().not('h1,h2,h3,h4,h5,h6,a,span.au-references__link,span.au-footnotes__link').length > 0) {
            // Find this term in the list and create a replacement for it.
            for (var i=0; i<settings.health.definitions.terms.length; i++) {
              var term = settings.health.definitions.terms[i];
              if (term['term'] === $(this).html()) {
                // Add counter in so we can create unique ids.
                term['counter'] = counter;
                // Create the replacement.
                var template = settings.health.definitions.templates[term['type']];
                for (var prop in term) {
                  if (term.hasOwnProperty(prop)) {
                    template = template.replace(new RegExp('{' + prop + '}', 'g'), term[prop]);
                  }
                }
                if ($(this).parent().length !== 0) {
                  $(this)[0].outerHTML = template;
                }
              }
            }
          }
        });
      }
    }
  };

})(jQuery);
