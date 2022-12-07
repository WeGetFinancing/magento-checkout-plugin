define([
    'Magento_Checkout/js/action/redirect-on-success',
    'Magento_Checkout/js/model/full-screen-loader',
    'mage/storage',
    'mage/url'
], function(redirectOnSuccessAction, fullScreenLoader, storage, url) {
   'use strict';

   return {
       execute: function(invId) {
           let payload = {};
           payload['invId'] = invId;
           storage.post(
               url.build('V1/carts/mine/wegetfinancing-set-order-inv-id'),
               JSON.stringify(payload),
               true
           ).done(
               function (json) {
                   let response = JSON.parse(json);
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
                       message: "Unknown server error, a ticket was opened to our operation team."
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
