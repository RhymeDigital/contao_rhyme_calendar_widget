
/**
 * Copyright (c) 2022 Rhyme Digital LLC (https://rhyme.digital)
 *
 * @license LGPL-3.0-or-later
 */


//Namespace
var Rhyme = window.Rhyme || {};
Rhyme.CalendarWidget = Rhyme.CalendarWidget || {};

//Encapsulate
(function ($) {

    let self;

    Rhyme.CalendarWidget.Events = {

        //Properties
        eventSubscribers:   {},       //The callbacks that have been registered
        env:                'prod',

        /*
         * Register the service
         */
        init: function(options){
            self = this;
            self.options = options || {};
            self.env = self.options.env || self.env;
            return self;
        },

        /*
         * Subscribe to a service
         *
         * @param string event
         * @param function callback
         * @param function bindTo
         */
        subscribe: function(event, callback, bindTo) {
            let uid = Rhyme.CalendarWidget.Util.getUniqueId();
            self.eventSubscribers[event] = self.eventSubscribers[event] || {};
            self.eventSubscribers[event][uid] = {'callback': callback, 'bindTo': bindTo};
            return uid;
        },

        /*
         * Unsubscribe from a service
         *
         * @param string event
         * @param string key
         */
        unsubscribe: function(event, key) {
            try {
                delete self.eventSubscribers[event][key];
            } catch (e) {
                self.log(e, 'error removing callback for event '+event+'... ');
            }
        },

        /*
         * Fire an event
         *
         * @param string event
         * @param object data
         */
        fire: function(event, data) {
            if (!self.eventSubscribers[event]) return;

            jQuery.each(self.eventSubscribers[event], function(key, val){
                let caller = val;
                try {
                    caller.callback(data, caller.bindTo);
                } catch (e) {
                    self.log(e, 'error executing callback '+event+'... ');
                }
            });
        },

        /*
         * Log a message
         * @param {object|string|int} data
         * @param {object|string|int} data
         */
        log: function(e, data) {
            if(self.env === 'dev') {
                console.log(data);
                console.log(e);
            }
        }

    };

    $(document).ready(function() {
        Rhyme.CalendarWidget.Events.init(); //Todo - update in production
    });

})(jQuery);