(function ($, Drupal, window, document) {

  'use strict';

  // To understand behaviors, see https://drupal.org/node/756722#behaviors
  Drupal.behaviors.backtotop = {
    attach: function (context, settings) {
      var url = '/' + settings.health.theme_path + '/images/icons/gototop.png';
      Vue.component('backtotop', {
        template: '<button title="back to top" class="goTop" v-if="isVisible" @click="backToTop"> <i class="fas fa-arrow-up fa-3x" aria-hidden="true"></i> </button>',
        data: function() {
          return {
            isVisible: false,
            imageLink: url
          };
        },
        methods: {
          initToTopButton: function() {
            $(document).bind('scroll', function() {
              var backToTopButton = $('.goTop');
              if ($(document).scrollTop() > 250) {
                backToTopButton.addClass('isVisible');
                this.isVisible = true;
              } else {
                backToTopButton.removeClass('isVisible');
                this.isVisible = false;
              }
            }.bind(this));
          },
          backToTop: function() {
            $('html,body', context).stop().animate({
              scrollTop: 0
            }, 'slow', 'swing');
          }
        },
        mounted: function() {
          this.$nextTick(function() {
            this.initToTopButton();
          });
        }
      });

      new Vue({
        el: '#backtotop',
      });

    }
  };
})(jQuery, Drupal, this, this.document);
