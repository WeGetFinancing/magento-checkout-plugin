define([
    'Magento_Checkout/js/action/redirect-on-success',
    'Magento_Checkout/js/model/full-screen-loader',
    'mage/storage',
    'Magento_Checkout/js/model/url-builder',
], function(redirectOnSuccessAction, fullScreenLoader, storage, urlBuilder) {
   'use strict';

    let config = window.checkoutConfig.payment.wegetfinancing_payment;

   return {
       execute: function(invId) {
           let data = {},
               payload = {};

           data['invId'] = invId;
           payload['request'] = JSON.stringify(data);

           storage.post(
               urlBuilder.createUrl(config.orderToInvIdPath, {}),
               JSON.stringify(payload),
               true
           ).done(
               function (json) {
                   let response = JSON.parse(json);

                   alert(json);

                   if (response.type === "SUCCESS") {
                       alert('success');
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
                       message: "Unknown server error on complete action, a ticket was opened to our operation team."
                   });
               }.bind(this)
           ).always(
               function() {
                   fullScreenLoader.stopLoader();
                   redirectOnSuccessAction.execute();
               }
           );
       }
   }
});
