(function() {
  "use strict";
  var $, Age,
    __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

  $ = jQuery;

  Age = (function() {
    Age.settings = {
      singular: 1,
      interval: 1000,
      suffixes: {
        past: "ago",
        future: "until"
      },
      prefixes: {
        past: "",
        future: ""
      },
      formats: {
        now: "now",
        singular: {
          seconds: "1s",
          minutes: "1m",
          hours: "1h",
          days: "1d",
          weeks: "1w",
          months: "1m",
          years: "1y"
        },
        plural: {
          seconds: "{{amount}}s",
          minutes: "{{amount}}m",
          hours: "{{amount}}h",
          days: "{{amount}}d",
          weeks: "{{amount}}w",
          months: "{{amount}}m",
          years: "{{amount}}y"
        }
      }
    };

    function Age($el, settings) { 

      if (settings == null) {
        settings = {};
      }

      this.text = __bind(this.text, this);
      this.interval = __bind(this.interval, this);
      this.format = __bind(this.format, this);
      this.unit = __bind(this.unit, this);
      this.amount = __bind(this.amount, this);
      this.formatting = __bind(this.formatting, this);
      this.adjust = __bind(this.adjust, this);
      this.prefix = __bind(this.prefix, this);
      this.suffix = __bind(this.suffix, this);
      this.date = __bind(this.date, this);
      this.reformat = __bind(this.reformat, this);
      this.$el = $el;
      this.settings = $.extend({}, Age.settings, settings);
      this.reformat();
    }

    Age.prototype.reformat = function() {
      var interval;
      interval = this.interval();
      this.$el.html(this.text(interval));
      return setTimeout(this.reformat, this.settings.interval);
    };

    Age.prototype.date = function() {

	var datetime_show = this.$el.attr('datetime').replace("-", "/"); 
	datetime_show = datetime_show.replace("-", "/"); 
	datetime_show = datetime_show.replace("-", "/"); 


	//return new Date(this.$el.attr('datetime') || this.$el.attr('date') || this.$el.attr('time'));
      return new Date(datetime_show);
    };

    Age.prototype.suffix = function(interval) {
      if (interval < 0) {
        return this.settings.suffixes.past;
      }
      if (interval > 0) {
        return this.settings.suffixes.future;
      }
    };

    Age.prototype.prefix = function(interval) {
      if (interval < 0) {
        return this.settings.prefixes.past;
      }
      if (interval > 0) {
        return this.settings.prefixes.future;
      }
    };

    Age.prototype.adjust = function(interval, scale) {
      return Math.round(Math.abs(interval / scale));
    };

    Age.prototype.formatting = function(interval) {
      return {
        seconds: this.adjust(interval, 1000),
        minutes: this.adjust(interval, 1000 * 60),
        hours: this.adjust(interval, 1000 * 60 * 60),
        days: this.adjust(interval, 1000 * 60 * 60 * 24),
        weeks: this.adjust(interval, 1000 * 60 * 60 * 24 * 7),
        months: this.adjust(interval, 1000 * 60 * 60 * 24 * 30),
        years: this.adjust(interval, 1000 * 60 * 60 * 24 * 365)
      };
    };

    Age.prototype.amount = function(formatting) {

      return formatting.years || formatting.months || formatting.weeks || formatting.days || formatting.hours || formatting.minutes || formatting.seconds || 0;
    };

    Age.prototype.unit = function(formatting) {
      return (formatting.years && "years") || (formatting.months && "months") || (formatting.weeks && "weeks") || (formatting.days && "days") || (formatting.hours && "hours") || (formatting.minutes && "minutes") || (formatting.seconds && "seconds") || void 0;
    };

    Age.prototype.format = function(amount, unit) {
      var _ref;
      return (_ref = this.settings.formats[amount === this.settings.singular ? 'singular' : 'plural']) != null ? _ref[unit] : void 0;
    };

    Age.prototype.interval = function() {
      return this.date() - new Date;
    };

    Age.prototype.text = function(interval) {
      var amount, format, formatting, prefix, suffix, unit;
      if (interval == null) {
        interval = this.interval();
      }
      suffix = this.suffix(interval);
      prefix = this.prefix(interval);
      formatting = this.formatting(interval);
      amount = this.amount(formatting);
      unit = this.unit(formatting);
      format = this.format(amount, unit);

      if (!format) {
        return this.settings.formats.now;
      }
      return "" + prefix + " " + (format.replace('{{unit}}', unit).replace('{{amount}}', amount)) + " " + suffix;
    };

    return Age;

  })();

  $.fn.extend({
    age: function(options) {
      if (options == null) {
        options = {};
      }
      return this.each(function() {
        return new Age($(this), options);
      });
    }
  });

}).call(this);
