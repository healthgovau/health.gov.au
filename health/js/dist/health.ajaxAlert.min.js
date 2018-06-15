(function ($, Drupal, window, document) {

  'use strict';

  var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

  // To understand behaviors, see https://drupal.org/node/756722#behaviors
  Drupal.behaviors.ajaxAlert = {
    attach: function (context, settings) {
      if (typeof Vue !== 'undefined') {

        var url = settings.health.base_url + '/news-and-events/health-alerts.xml';

        // Function to call feed and assign values to array.
        var ajaxMixin = {
          methods: {
            callFeed: function (url) {
              // No cookie is set to hide block.
              var items = [];
              var scope = this;

              // Make AJAX call.
              $.ajax({
                type: 'GET',
                url: url,
                dataType: 'xml',
                success: function (response) {
                  var xml = $(response);
                  xml.find('item').each(function () {

                    // Check if the node list appears in cookies already.
                    var updated_date = new Date($(this).find('pubDate').text().replace(/\s/, 'T')).getTime();
                    var cookie = 'HideHealthAlert-' + $(this).find('id').text() + '-' + updated_date;
                    var hideAlert = scope.getCookie(cookie);
                    if (!hideAlert) {
                      // Cookie is set to hide the block.
                      items.push(
                        {
                          'id': $(this).find('id').text(),
                          'title': $(this).find('title').text(),
                          'link': $(this).find('link').text(),
                          'updated_date': updated_date
                        });
                    }
                  });

                  // Check if there is data returned.
                  if (items.length === 0) {
                    scope.show = false;
                  }
                  else {
                    // Set global variable data.
                    scope.show = true;
                    scope.items = items;
                  }
                },
                error: function (error) {
                  scope.show = false;
                }
              });
            }
          }
        };

        // Functions to hide alert block and set cookie to hide alert block.
        var cookieMixin = {
          methods: {
            hideAlert: function (event) {
              this.show = false;
              var scope = this;

              $.each(this.items, function (index, value) {
                // Set cookie to hide alert for 100 years.
                scope.setCookie('HideHealthAlert' + '-' + value.id + '-' + value.updated_date, true, 100)
              });
            },
            setCookie: function (name, value, years) {
              var expires = "";
              if (years) {
                var date = new Date();
                date.setTime(date.getTime() + (years * 365 * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
              }
              document.cookie = name + "=" + (value || "") + expires + "; path=/";
            },
            getCookie: function (name) {
              var nameEQ = name + "=";
              var ca = document.cookie.split(';');
              for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
              }
              return null;
            },
          }
        };

        // Vue filter to convert timestamp to dd MM yy format.
        Vue.filter('formatDate', function (value) {
          if (value) {
            var date = new Date(parseInt(value));
            return date.getUTCDate() + ' ' + months[(date.getUTCMonth()+1)] + ' ' + date.getUTCFullYear();
          }
        });

        new Vue({
          el: '#health-alert-ajax-container',
          data: {
            items: [],
            show: false
          },
          created: function () {
            this.callFeed(url)
          },
          mixins: [ajaxMixin, cookieMixin],
        });
      }
    }
  }

})(jQuery, Drupal, this, this.document);
