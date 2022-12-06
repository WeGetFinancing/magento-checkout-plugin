define([
    'Magento_Checkout/js/action/redirect-on-success',
    'Magento_Checkout/js/model/full-screen-loader',
    'mage/storage',
    'mage/url'
], function(redirectOnSuccessAction, fullScreenLoader, storage, url) {
   'use strict';

   return {
       execute: function(invId) {
           storage.post(
               url.build('testing'),
               JSON.stringify(payload),
               true
           ).done(
               function (json) {

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
