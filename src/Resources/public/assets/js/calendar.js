/**
 * Copyright (C) 2022 Rhyme Digital, LLC.
 *
 * @link		https://rhyme.digital
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


var Rhyme = window.Rhyme || {};
Rhyme.CalendarWidget = Rhyme.CalendarWidget || {};

(function ($) {

	// region Private Variables

	let _blnDoInit                  = true,
		_blnInitialized             = false,
		_registered					= {}
	;

	// endregion

	/**
	 * Set everything up
	 * @private
	 */
	function _setup() {
		if (!_blnDoInit) return;
		_blnDoInit = false;

		/**
		 * RhymeCalendarWidgetCalendar class
		 */
		class RhymeCalendarWidgetCalendar {

			/**
			 * RhymeCalendarWidgetCalendar constructor
			 * @param options
			 */
			constructor(options) {
				let self = this;

				//self.element 			= Rhyme.CalendarWidget.Util.getJqEl(element);
				self.doEventsSetup 		= true;
				self.options 			= options || {};
				self.calendarOptions 	= self.options.calendarOptions || {};
				self.sendToDatasource	= self.options.sendToDatasource || false;

				// Add a data attribute to the wrapper and store in registered
				let intId = Rhyme.CalendarWidget.Util.getUniqueId();
				//self.element.attr('data-rhymeToastCalendarId', intId);
				self.id = intId;
				_registered[intId] = self;
			}


			/**
			 *
			 * @param data
			 */
			createEvent(data) {
				let self = this;

				self.sendAddUpdateRequest(data)
					.done(function(response){
						if (response && response.success && response.event) {
							Rhyme.CalendarWidget.Events.fire('postRhymeCalendarAddEvent', {
								response:	response,
								data: 		data,
								source:    	self
							});
						}
					});
			}


			/**
			 *
			 * @param data
			 */
			updateEvent(data) {
				let self = this;

				self.sendAddUpdateRequest(data)
					.done(function(response){
						if (response && response.success && response.event) {
							Rhyme.CalendarWidget.Events.fire('postRhymeCalendarUpdateEvent', {
								response:	response,
								data: 		data,
								source:    	self
							});
						}
					});
			}


			/**
			 *
			 * @param data
			 */
			deleteEvent(data) {
				let self = this;

				self.sendDeleteRequest(data)
					.done(function(response){
						if (response && response.success) {
							Rhyme.CalendarWidget.Events.fire('postRhymeCalendarDeleteEvent', {
								response:	response,
								source:    	self
							});
						}
					});
			}


			/**
			 *
			 * @param e
			 */
			getEventPostData(e) {
				let self = this,
					rt	 = jQuery('input[name=REQUEST_TOKEN]'),
					data = {
						REQUEST_TOKEN:      	rt.length ? rt.val() : Rhyme.CalendarWidget.Util.getParameterFromString(location.href, 'rt'),
						calendarId:				self.options.calendarId || 0,
						calendarName:			self.options.calendarName || '',
						calendarFromTable:		self.options.calendarFromTable || '',
						calendarRowId:			self.options.calendarRowId || 0,
						eventId:				e.id,
						eventName:				e.title,
						eventStartTime:			_getFormattedDate(e.start.d),
						eventEndTime:			_getFormattedDate(e.end.d),
						eventIsAllDay:			e.isAllday ? 1 : '', // Yes, this is a lowercase "d"
						eventDescription:		e.body,
						widgetClassName:		Rhyme.CalendarWidget.Util.getParameterFromString(location.href, 'widgetClassName') // Todo: get this for FE too
					}
				;

				Rhyme.CalendarWidget.Events.fire('getToastCalendarPostData', {
					data:		data,
					e:			e,
					source:    	self
				});

				return data;
			}


			/**
			 *
			 * @param post
			 */
			sendAddUpdateRequest(post) {
				let self = this;

				Rhyme.CalendarWidget.Events.fire('preSendAddUpdateCalendarEventRequest', {
					post:		post,
					source:    	self
				});

				return jQuery.ajax({
					url: '/rhyme_calendar_widget/rhyme_addUpdateCalendarEvent',
					cache: false,
					method: 'POST',
					data: jQuery.param(post)
				})
					.done(function(data) {
						if (!data) {
							alert('Invalid response.');
						}
						else if (!data.success) {
							alert(data.message);
						}

						Rhyme.CalendarWidget.Events.fire('postSendAddUpdateCalendarEventRequest', {
							post:		post,
							data:		data,
							source:    	self
						});
					});
			}


			/**
			 *
			 * @param post
			 */
			sendDeleteRequest(post) {
				let self = this;

				Rhyme.CalendarWidget.Events.fire('preSendDeleteCalendarEventRequest', {
					post:		post,
					source:    	self
				});

				return jQuery.ajax({
					url: '/rhyme_calendar_widget/rhyme_deleteCalendarEvent',
					cache: false,
					method: 'POST',
					data: jQuery.param(post)
				})
					.done(function(data) {
						if (!data) {
							alert('Invalid response.');
						}
						else if (!data.success) {
							alert(data.message);
						}

						Rhyme.CalendarWidget.Events.fire('postSendDeleteCalendarEventRequest', {
							data:		data,
							source:    	self
						});
					});
			}

		}

		// region Static Methods

		/**
		 *
		 * @param date
		 * @returns {string}
		 * @private
		 */
		function _getFormattedDate(date) {
			return date.getFullYear() + '-' + (date.getMonth() + 1).toString().padStart(2, '0') + '-' + date.getDate().toString().padStart(2, '0') + ' ' +  date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0') + ':' + date.getSeconds().toString().padStart(2, '0')
		}

		// endregion

		// region Public Methods

		Rhyme.CalendarWidget.Calendar = {

			/**
			 * Create a new RhymeCalendarWidgetCalendar instance
			 * @param options
			 * @returns {RhymeCalendarWidgetCalendar}
			 */
			create: function (options) {
				return new RhymeCalendarWidgetCalendar(options);
			},

			/**
			 * Return a class instance using its ID
			 * @param dataId
			 * @returns {RhymeCalendarWidgetCalendar|null}
			 */
			getInstanceById: function (dataId) {
				return _registered && _registered.hasOwnProperty(dataId) ? _registered[dataId] : null;
			},

			/**
			 * Return a date formatted for the database
			 * @param date
			 * @returns {string}
			 */
			getFormattedDate: function (date) {
				return _getFormattedDate(date);
			}
		};

		// endregion

		_blnInitialized = true;

	}

	jQuery(document).ready(_setup);

}(jQuery));
