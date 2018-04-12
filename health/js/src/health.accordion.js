(function ($, Drupal, window, document) {

  'use strict';

  Drupal.health = Drupal.health || {};
  Drupal.health.accordion = {};

  Drupal.health.accordion.toggle = function($element, speed, openCallback, closeCallback) {
    if (!$('#' + $element.attr('aria-controls')).hasClass("health-accordion--open")) {
      openCallback();
    }
    $element.toggleClass("health-accordion--open");
    $('#' + $element.attr('aria-controls')).toggleClass("health-accordion--open");
    $('#' + $element.attr('aria-controls')).animate({
      height: "toggle"
    },speed, function () {
      if (!$('#' + $element.attr('aria-controls')).hasClass("health-accordion--open")) {
        closeCallback();
      }
    });
  };

})(jQuery, Drupal, this, this.document);
