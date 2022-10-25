/**
 * Copyright (C) 2022 Rhyme Digital, LLC.
 *
 * @link		https://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

//Namespace
var Rhyme = window.Rhyme || {};
Rhyme.CalendarWidget = Rhyme.CalendarWidget || {};

//Encapsulate
(function ($) {

	Rhyme.CalendarWidget.Util = jQuery.extend({}, (Rhyme.CalendarWidget.Util || {}), {

        /**
         * Strip comments
         * @param str
         * @returns {*}
         */
        stripComments: function (str) {
            return str.replace(/<\!--.*?-->/g, ""); //Remove comments
        },

        /**
         * Turn a spinner on/off inside a designated element
         * @param state
         * @param el
         */
        toggleModalSpinner: function (state, el) {
            let container = Rhyme.CalendarWidget.Util.getJqEl(el);
            let spinner = jQuery('#spinner');
            if(spinner.length > 0 && state==='off') {
                spinner.remove();
            } else if (state!=='off') {
                if (!spinner.length) {
                    spinner = jQuery('<div id="spinner"><div class="spinnee"></div></div>');
                    container.append(spinner);
                }
            }
        },

        /**
         * Check for IE
         * @returns {boolean}
         */
        isIE: function() {
            let ua = window.navigator.userAgent;
            let msie = ua.indexOf("MSIE ");
            return msie !== -1 || !!navigator.userAgent.match(/Trident.*rv\:11\./);
        },

		/**
		 * Get a single parameter from a URL string
		 * @param string
		 * @param param
		 * @returns {*}
		 */
		getParameterFromString(string, param) {
			let params = string.slice(string.indexOf('?') + 1).split('&');
			for (let i = 0; i < params.length; i++) {
				let urlparam = params[i].split('=');
				if (urlparam[0] === param) {
					return urlparam[1];
				}
			}
			return false;
		},

		/**
		 *
		 * @param number
		 * @param decimals
		 * @param dec_point
		 * @param thousands_sep
		 * @returns {string}
		 */
		numberFormat (number, decimals, dec_point, thousands_sep) {
			// Strip all characters but numerical ones.
			number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
			var n = !isFinite(+number) ? 0 : +number,
				prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
				sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
				dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
				s = '',
				toFixedFix = function (n, prec) {
					var k = Math.pow(10, prec);
					return '' + Math.round(n * k) / k;
				};
			// Fix for IE parseFloat(0.55).toFixed(0) = 0;
			s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
			if (s[0].length > 3) {
				s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
			}
			if ((s[1] || '').length < prec) {
				s[1] = s[1] || '';
				s[1] += new Array(prec - s[1].length + 1).join('0');
			}
			return s.join(dec);
		},


		/**
		 * Make sure the element is a jQuery element
		 * @param el
		 * @returns {*}
		 */
		getJqEl(el) {
			return el instanceof jQuery ? el : jQuery(el);
		},

		/**
		 *
		 * @return {number}
		 */
		getUniqueId() {
			return Date.now()+Math.floor(Math.random() * 999999);
		}

    });

}(jQuery));