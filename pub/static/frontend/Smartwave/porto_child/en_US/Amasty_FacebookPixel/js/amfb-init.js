/**
 * Facebook pixel initialization
 */

define([
    'underscore',
    'Magento_Customer/js/customer-data',
    'Amasty_FacebookPixel/js/action/amfb-actions',
    'Amasty_FacebookPixel/js/model/amfb-model'
], function (_, customerData, fbActions, fbModel) {
    "use strict";

    return function (options) {
        var fbSectionName = 'amfacebook-pixel';

        fbModel.setLogEventUrl(options.loggingUrl);
        fbModel.setIsLogEnabled(options.isLogEnabled);
        fbModel.actions.subscribe(function (eventsData) {
            if (!_.isEmpty(eventsData)) {
                fbActions.setFbqData(eventsData)
            }
        })

        if (options.eventsData) {
            fbModel.actions(options.eventsData)
        }

        customerData.get(fbSectionName).subscribe(function (loadedData) {
            if (!_.isEmpty(loadedData.events)) {
                fbModel.actions(JSON.stringify(loadedData.events));

                customerData.set(fbSectionName, {});
            }
        });

        customerData.reload([fbSectionName]);
    }
});
