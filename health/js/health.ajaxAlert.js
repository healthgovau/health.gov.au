(function ($, Drupal, window, document) {

  'use strict';

  // To understand behaviors, see https://drupal.org/node/756722#behaviors
  Drupal.behaviors.ajaxAlert = {
    attach: function (context, settings) {
      var url = settings.health.base_url + '/test-feed';

      // define a mixin object
      var myMixin = {
        methods: {
          callFeed: function ( url ) {
            var titles = [];
            var scope = this;

            $.ajax({
              type: 'GET',
              url: url,
              dataType: 'xml',
              success: function (response) {
                var xml = $(response);
                xml.find('item').each(function () {
                  titles.push(
                    {
                      'id': $(this).find('title').text(),
                      'title': $(this).find('title').text(),
                    });
                });
                scope.loading = false;
                scope.titles = titles;
              },
              error: function (error) {
                scope.loading = true;
              }
            });
          }
        }
      };

      var PulseLoader = VueSpinner.PulseLoader;

      var resultItem = Vue.component('result-item', {
        template: '\
          <li >\
            {{ title }}\
          </li>\
        ',
        props: ['title'],
      });

      new Vue({
        el: '#ajax-container',
        data: {
          loading: true,
          titles: [],
          size: '1em',
          color: '#3AB982',
          height: '35px',
          width: '4px',
          margin: '2px',
          radius: '2px'
        },
        mounted: function () {
          this.callFeed(url)
        },
        mixins: [myMixin],
        components: {
          PulseLoader,
          resultItem
        }
      });
    }
  }

})(jQuery, Drupal, this, this.document);
