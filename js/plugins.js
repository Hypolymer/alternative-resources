
// Avoid `console` errors in browsers that lack a console. Taken from HTML5 boilerplate (https://html5boilerplate.com/)
(function() {
  var method;
  var noop = function () {};
  var methods = [
  'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
  'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
  'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
  'timeline', 'timelineEnd', 'timeStamp', 'trace', 'warn'
  ];
  var length = methods.length;
  var console = (window.console = window.console || {});

  while (length--) {
    method = methods[length];

    // Only stub undefined methods.
    if (!console[method]) {
      console[method] = noop;
    }
  }
}());

// Place any jQuery/helper plugins in here.

/**
* Spin.js
* fgnass.github.com/spin.js#v2.0.1
* Copyright (c) 2011-2014 Felix Gnass
* Licensed under the MIT license
*/

(function(factory) {

  if (typeof exports == 'object') {
    // CommonJS
    factory(require('jquery'), require('spin'))
  }
  else if (typeof define == 'function' && define.amd) {
    // AMD, register as anonymous module
    define(['jquery', 'spin'], factory)
  }
  else {
    // Browser globals
    if (!window.Spinner) throw new Error('Spin.js not present')
      factory(window.jQuery, window.Spinner)
    }

  }(function($, Spinner) {

    $.fn.spin = function(opts, color) {

      return this.each(function() {
        var $this = $(this),
        data = $this.data();

        if (data.spinner) {
          data.spinner.stop();
          delete data.spinner;
        }
        if (opts !== false) {
          opts = $.extend(
            { color: color || $this.css('color') },
            $.fn.spin.presets[opts] || opts
          )
          data.spinner = new Spinner(opts).spin(this)
        }
      })
    }

    $.fn.spin.presets = {
      tiny: { lines: 8, length: 2, width: 2, radius: 3 },
      small: { lines: 8, length: 4, width: 3, radius: 5 },
      large: { lines: 10, length: 8, width: 4, radius: 8 }
    }

  }));
