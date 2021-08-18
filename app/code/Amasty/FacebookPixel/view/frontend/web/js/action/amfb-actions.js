/**
 * Facebook pixel actions
 * @api
 */

define([
    'jquery',
    'Amasty_FacebookPixel/js/model/amfb-model'
], function ($, amFbModel) {
    'use strict';

    /**
     * Call facebook actions with data
     * @param {Object|String} eventsData
     */
    function setFbqData(eventsData) {
        $.each(JSON.parse(eventsData), function (key, event) {
            fbq(event.event_action, event.event_type, event.event_data);
        });

        logEventData(eventsData);
    }

    /**
     * Log facebook events with data
     * @param {Object|String} eventsData
     */
    function logEventData(eventsData) {
        var logUrl = amFbModel.getLogEventUrl();

        if (logUrl && amFbModel.isLogEnabled()) {
            $.ajax({
                url: logUrl,
                data: {events: JSON.parse(eventsData)},
                type: 'post',
                dataType: 'json',
                showLoader: false,
            });
        }
    }

    return {
        setFbqData: setFbqData
    }
});
