define([
    'Magento_Checkout/js/view/payment/default',
    'Magento_Checkout/js/model/full-screen-loader',
    'mage/storage',
    'Magento_Checkout/js/model/url-builder',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/totals',
    'WeGetFinancing_Checkout/js/action/complete',
    'jquery'
], function(Component, fullScreenLoader, storage,urlBuilder, customer,totals,completeAction, $) {
    'use strict';

    var config = window.checkoutConfig.payment.wegetfinancing_payment;

    return Component.extend({
        defaults: {
            template: 'WeGetFinancing_Checkout/payment/wegetfinancing-checkout',
            code: 'wegetfinancing_payment',
            isClicked: false,
            superSelf: null,
            redirectAfterPlaceOrder: false
        },

        initialize: function () {
            this._super();
            var self = this;

            require([config.sdkUrl]);

            window. weOnButtonClick = function () {
                let serviceUrl,
                    data = {},
                    form_obj = $("#co-shipping-form").serializeArray(),
                    payload = {},
                    response = {};

                if (!customer.isLoggedIn()) {
                    serviceUrl = urlBuilder.createUrl('/guest-carts/mine/wegetfinancing-generate-funnel-url', {});
                } else {
                    serviceUrl = urlBuilder.createUrl('/carts/mine/wegetfinancing-generate-funnel-url', {});
                }

                form_obj.forEach(function(inputObj){
                    data[inputObj.name] = inputObj.value;
                });

                data['email'] = $("#customer-email").val();
                data['shipping_amount'] = totals.totals()['shipping_amount'];

                payload['request'] = JSON.stringify(data);

                fullScreenLoader.startLoader();

                storage.post(
                    serviceUrl,
                    JSON.stringify(payload),
                    true
                ).done(
                    function (json) {
                        response = JSON.parse(json);

                        if (response.type === "SUCCESS") {
                            new GetFinancing(
                                response.data.href,
                                function() {
                                    self.placeOrder();
                                }.bind(self),
                                function() {}
                            )
                            return;
                        }

                        if (response.type === "ERROR") {
                            response.messages.forEach(
                                function (message) {
                                    this.messageContainer.addErrorMessage({
                                        message: message.message
                                    });
                                }.bind(this)
                            )
                        }
                    }.bind(this)
                ).fail(
                    function () {
                        this.messageContainer.addErrorMessage({
                            message: "Unknown server error, a ticket was opened to our operation team."
                        });
                    }.bind(this)
                ).always(
                    function() {
                        fullScreenLoader.stopLoader();
                    }
                );
            }.bind(self).bind(this)
        },

        getData: function () {
            return {
                'method': this.getCode(),
                'additional_data': null
            };
        },

        getTitle: function () {
            return config.title;
        },

        getCode: function () {
            return this.code;
        },

        isActive: function () {
            // return this.getCode() === this.isChecked();
            return true
        },

        getPaymentCardSrc: function () {
            return config.paymentCardSrc;
        },


        afterPlaceOrder: function () {
            completeAction.execute();
        },


    });
});

