define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';

        rendererList.push({
            type: 'wegetfinancing_payment',
            component: 'WeGetFinancing_Checkout/js/view/payment/method-renderer/wegetfinancing-checkout'
        });

        return Component.extend({});
    }
);
