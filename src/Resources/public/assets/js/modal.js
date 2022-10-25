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

    let _blnDoInit                  = true,
        _blnInitialized             = false,
		_modal				        = []
    ;

	/**
     * Set up the service
	 * @private
	 */
	function _setup() {
        if (!_blnDoInit) return;
        _blnDoInit = false;

		_modal = jQuery('#calendarWidgetModal .modal');

		_configureMaskClicks();

        _blnInitialized = true;
    }


	/**
     * Close the modal when the mask is clicked
	 * @private
	 */
	function _configureMaskClicks() {
		jQuery('body').on('click', '.modal-mask', function(e){
		    let target = jQuery(e.target);
		    if (!target.hasClass('modal-mask') || !target.children('.modal').length) return;

			e.preventDefault();
			_closeModal();
			return false;
		});
	}


    /**
     * Open the modal window
	 * @param options
	 * @private
     */
    function _openModal(options) {
		Rhyme.CalendarWidget.Events.fire('modal_opening', {
            modal: 	    _modal,
            options:    options
        });

        _modal.closest('.modal-mask').eq(0).removeClass('invisible');
        jQuery('body').addClass('modal-open');

		Rhyme.CalendarWidget.Events.fire('modal_opened', {
            modal: 	    _modal,
            options:    options
        });

        return false;
    }


	/**
	 * Close the modal window
	 * @param options
	 * @private
	 */
	function _closeModal(options) {
		Rhyme.CalendarWidget.Events.fire('modal_closing', {
			modal: 	    _modal,
			options:    options
        });

		_modal.closest('.modal-mask').eq(0).addClass('invisible');
        jQuery('body').removeClass('modal-open');
		_clearContent();

		Rhyme.CalendarWidget.Events.fire('modal_closed', {
			modal: 	    _modal,
			options:    options
        });
    }


	/**
	 * Set the modal window content
	 * @param content
	 * @param options
	 * @private
	 */
	function _setContent(content, options) {
	    let previousContent = jQuery('.content', _modal).html();

		Rhyme.CalendarWidget.Events.fire('modal_content_setting', {
			modal: 	            _modal,
			content:            content,
            previousContent:    previousContent,
			options:            options
		});

		// Todo: add spinner here?

		_modal.children('.inner').animate({ scrollTop: 0 });
		jQuery('.content', _modal).html(content);

		Rhyme.CalendarWidget.Events.fire('modal_content_set', {
			modal: 	            _modal,
			content:            content,
			previousContent:    previousContent,
			options:            options
		});
	}


	/**
	 * Clear the modal window content
	 * @param options
	 * @private
	 */
	function _clearContent(options) {
		let previousContent = jQuery('.content', _modal).html();

		Rhyme.CalendarWidget.Events.fire('modal_content_clearing', {
			modal: 	            _modal,
			previousContent:    previousContent,
			options:            options
		});

		// Todo: add spinner here?

		_modal.children('.inner').animate({ scrollTop: 0 });
		jQuery('.content', _modal).html('');

		Rhyme.CalendarWidget.Events.fire('modal_content_cleared', {
			modal: 	            _modal,
			previousContent:    previousContent,
			options:            options
		});
	}


    // Public
	Rhyme.CalendarWidget.Modal = {

		/**
		 * Get the modal window
		 * @public
		 */
		getModal: function() {
			return _modal;
		},

        /**
         * Open the modal window
		 * @param options
         * @public
         */
        openModal: function(options) {
            _openModal(options);
        },

		/**
		 * Close the modal window
		 * @param options
		 * @public
		 */
		closeModal: function(options) {
            _closeModal(options);
        },

		/**
		 * Set the modal window content
		 * @param content
		 * @param options
		 * @public
		 */
		setContent: function(content, options) {
			_setContent(content, options);
		},

		/**
		 * Clear the modal window content
		 * @param options
		 * @public
		 */
		clearContent: function(options) {
			_clearContent(options);
		}
    };


    jQuery(document).ready(_setup);

}(jQuery));


