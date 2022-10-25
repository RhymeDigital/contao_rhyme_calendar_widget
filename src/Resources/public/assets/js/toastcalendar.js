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
		 * RhymeCalendarWidgetToastCalendar class
		 */
		class RhymeCalendarWidgetToastCalendar {

			/**
			 * RhymeCalendarWidgetToastCalendar constructor
			 * @param element
			 * @param options
			 */
			constructor(element, options) {
				let self = this;

				self.element 			= Rhyme.CalendarWidget.Util.getJqEl(element);
				self.doEventsSetup 		= true;
				self.options 			= options || {};
				self.calendarOptions 	= self.options.calendarOptions || {};
				self.sendToDatasource	= self.options.sendToDatasource || false;

				// Add a data attribute to the wrapper and store in registered
				let intId = Rhyme.CalendarWidget.Util.getUniqueId();
				self.element.attr('data-rhymeToastCalendarId', intId);
				self.id = intId;
				_registered[intId] = self;

				// Load the Toast UI calendar
				if (!self.loadToastCalendar()) {
					return;
				}

				// Set the events for this instance
				self.setEvents();
			}

			/**
			 * Load calendar
			 */
			loadToastCalendar() {
				let self = this;
				if (self.toastCalendar) return;
				if (!window.tui.Calendar) {
					console.error('Toast UI Calendar not loaded.');
					return false;
				}

				self.toastCalendar = new window.tui.Calendar(
					self.element.find('.calendarElement').get(0),
					self.calendarOptions
				);

				self.displayRenderRange();

				if (self.options.events && self.options.events.length) {
					self.options.events.forEach(function(data){
						self.toastCalendar.createEvents([{
							id: 			data.id,
							calendarId: 	self.options.calendarId || 0,
							title: 			data.name,
							body: 			data.description,
							start:			data.startTime,
							end:			data.endTime,
							isReadOnly:		!!data.isReadOnly,
							isAllday:		!!data.isAllDay // Yes, this is a lowercase "d"
						}]);
					});
				}

				return true;
			}

			/**
			 * Set events
			 */
			setEvents() {
				let self = this;
				if (!self.doEventsSetup || !self.toastCalendar || !window.tui.Calendar) return;
				self.doEventsSetup = false;

				self.toastCalendar.on('beforeCreateEvent', (e) => {
					self.beforeCreateEvent(e, self);
				});

				self.toastCalendar.on('beforeUpdateEvent', (e) => {
					self.beforeUpdateEvent(e, self);
				});

				self.toastCalendar.on('beforeDeleteEvent', (e) => {
					self.beforeDeleteEvent(e, self);
				});

				self.element.on('click', '.calendarPrevButton', function(e){
					e.preventDefault();
					self.toastCalendar.prev();
					self.displayRenderRange();
					return false;
				});

				self.element.on('click', '.calendarNextButton', function(e){
					e.preventDefault();
					self.toastCalendar.next();
					self.displayRenderRange();
					return false;
				});

				self.element.on('click', '.calendarTodayButton', function(e){
					e.preventDefault();
					self.toastCalendar.today();
					self.displayRenderRange();
					return false;
				});

				self.element.on('change', '.changeCalendarViewSelect', function(e){
					e.preventDefault();
					self.toastCalendar.changeView(jQuery(e.currentTarget).val());
					self.displayRenderRange();
					return false;
				});

				// If we're in a modal, destroy the Toast calendar on close
				if (self.element.parents('.modal').length) {
					self.modalClosingEventUid = Rhyme.CalendarWidget.Events.subscribe('modal_closing', function (){
						self.toastCalendar.destroy();
						Rhyme.CalendarWidget.Events.unsubscribe('modal_closing', self.modalClosingEventUid);
						delete _registered[self.id];
					});
				}
			}


			/**
			 *
			 * @param e
			 * @param self
			 */
			beforeCreateEvent(e, self) {
				let fnCreateToastEvent = (id) => {

					Rhyme.CalendarWidget.Events.fire('preToastCalendarCreateEvents', {
						e:			e,
						id: 	    id,
						source:    	self
					});

					Rhyme.CalendarWidget.toastCalendar.createEvents([{
						...e,
						id: id
					}]);

					Rhyme.CalendarWidget.Events.fire('postToastCalendarCreateEvents', {
						e:			e,
						id: 	    id,
						source:    	self
					});
				};

				if (!self.sendToDatasource) {
					fnCreateToastEvent(Date.now() + Math.round(Math.random(1000000,9999999) * 1000000));
					return;
				}

				self.sendAddUpdateRequest(self.getEventPostData(e))
					.done(function(data){
						if (data && data.success && data.event) {
							fnCreateToastEvent(data.event.id);
						}
					});
			}


			/**
			 *
			 * @param e
			 * @param self
			 */
			beforeUpdateEvent(e, self) {
				let fnUpdateToastEvent = () => {

					Rhyme.CalendarWidget.Events.fire('preToastCalendarUpdateEvent', {
						e:			e,
						source:    	self
					});

					self.toastCalendar.updateEvent(e.event.id, e.event.calendarId, e.changes);

					Rhyme.CalendarWidget.Events.fire('postToastCalendarUpdateEvent', {
						e:			e,
						source:    	self
					});
				};

				if (!self.sendToDatasource) {
					fnUpdateToastEvent();
					return;
				}

				self.sendAddUpdateRequest(self.getEventPostData(jQuery.extend({}, e.event, e.changes)))
					.done(function(data){
						if (data && data.success && data.event) {
							fnUpdateToastEvent();
						}
					});
			}


			/**
			 *
			 * @param e
			 * @param self
			 */
			beforeDeleteEvent(e, self) {
				let fnDeleteToastEvent = () => {

					Rhyme.CalendarWidget.Events.fire('preToastCalendarDeleteEvent', {
						e:			e,
						source:    	self
					});

					self.toastCalendar.deleteEvent(e.id, e.calendarId);

					Rhyme.CalendarWidget.Events.fire('postToastCalendarDeleteEvent', {
						e:			e,
						source:    	self
					});
				};

				if (!self.sendToDatasource) {
					fnDeleteToastEvent();
					return;
				}

				self.sendDeleteRequest(e.id)
					.done(function(data){
						if (data && data.success) {
							fnDeleteToastEvent();
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

				Rhyme.CalendarWidget.Events.fire('preSendAddUpdateToastCalendarRequest', {
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

						Rhyme.CalendarWidget.Events.fire('postSendAddUpdateToastCalendarRequest', {
							post:		post,
							data:		data,
							source:    	self
						});
					});
			}


			/**
			 *
			 * @param id
			 */
			sendDeleteRequest(id) {
				let self = this;

				Rhyme.CalendarWidget.Events.fire('preSendDeleteToastCalendarRequest', {
					id:			id,
					source:    	self
				});

				return jQuery.ajax({
					url: '/rhyme_calendar_widget/rhyme_deleteCalendarEvent',
					cache: false,
					method: 'POST',
					data: jQuery.param({
						eventId: id
					})
				})
					.done(function(data) {
						if (!data) {
							alert('Invalid response.');
						}
						else if (!data.success) {
							alert(data.message);
						}

						Rhyme.CalendarWidget.Events.fire('postSendDeleteToastCalendarRequest', {
							id:			id,
							data:		data,
							source:    	self
						});
					});
			}


			displayRenderRange() {
				let self = this,
					rangeStart = self.toastCalendar.getDateRangeStart(),
					rangeEnd = self.toastCalendar.getDateRangeEnd()
				;
				self.element.find('.calendarTitle').text(_getNavbarRange(rangeStart, rangeEnd, self.toastCalendar.getViewName()));
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

		/**
		 *
		 * @param tzStart
		 * @param tzEnd
		 * @param viewType
		 * @returns {string|*}
		 * @private
		 */
		function _getNavbarRange(tzStart, tzEnd, viewType) {
			var start = tzStart.toDate();
			var end = tzEnd.toDate();
			var middle;
			moment.locale('en');
			if (viewType === 'month' || viewType === 'week') {
				middle = new Date(start.getTime() + (end.getTime() - start.getTime()) / 2);
				return moment(middle).format('MMM YYYY');
			}
			if (viewType === 'day') {
				return moment(start).format('MMM DD, YYYY');
			}
			throw new Error('no view type');
		}

		// endregion

		// region Public Methods

		Rhyme.CalendarWidget.ToastCalendar = {

			/**
			 * Create a new RhymeCalendarWidgetToastCalendar instance
			 * @param element
			 * @param options
			 * @returns {RhymeCalendarWidgetToastCalendar}
			 */
			create: function (element, options) {
				return new RhymeCalendarWidgetToastCalendar(element, options);
			},

			/**
			 * Return a class instance using its ID
			 * @param dataId
			 * @returns {RhymeCalendarWidgetToastCalendar|null}
			 */
			getInstanceById: function (dataId) {
				return _registered && _registered.hasOwnProperty(dataId) ? _registered[dataId] : null;
			}
		};

		// endregion

		_blnInitialized = true;

	}

	jQuery(document).ready(_setup);

}(jQuery));
