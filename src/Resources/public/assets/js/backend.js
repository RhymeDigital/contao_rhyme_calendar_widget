/**
 * Copyright (C) 2022 Rhyme Digital, LLC.
 *
 * @link		https://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

'use strict';

//Namespace
var Rhyme = window.Rhyme || {};
Rhyme.CalendarWidget = Rhyme.CalendarWidget || {};

//Encapsulate
(function ($) {

    // Private

    /**
     * Open a calendar in a modal window
     * @param options
     * @private
     */
    function _openCalendarModal(options) {
        var opt = options || {},
            maxWidth = (window.getSize().x - 20).toInt(),
            maxHeight = (window.getSize().y - 192).toInt();
        if (!opt.id) opt.id = 'tl_select';
        if (!opt.width || opt.width > maxWidth) opt.width = Math.min(maxWidth, 900);
        if (!opt.height || opt.height > maxHeight) opt.height = maxHeight;
        var M = new SimpleModal({
            'width': opt.width,
            'draggable': false,
            'overlayOpacity': .7,
            'onShow': function() { document.body.setStyle('overflow', 'hidden'); },
            'onHide': function() { document.body.setStyle('overflow', 'auto'); }
        });
        M.addButton(Contao.lang.cancel, 'btn', function() {
            if (this.buttons[0].hasClass('btn-disabled')) {
                return;
            }
            this.hide();
        });
        M.addButton(Contao.lang.apply, 'btn primary', function() {
            if (this.buttons[1].hasClass('btn-disabled')) {
                return;
            }
            if (opt.callback) {
                opt.callback();
            }
            this.hide();
        });
        M.show({
            'title': opt.title,
            'contents': '<iframe src="' + opt.url + '" name="simple-modal-iframe" width="100%" height="' + opt.height + '" frameborder="0"></iframe>',
            'model': 'modal'
        });
    }


    // Public
    Rhyme.CalendarWidget.Backend = {

        /**
         * Open a calendar in a modal window
         *
         * @param {object} options An optional options object
         */
        openCalendarModal: function(options) {
            _openCalendarModal(options);
        }
    };

}(document.id));


