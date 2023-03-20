define([
    'Magento_Checkout/js/action/redirect-on-success',
    'Magento_Checkout/js/model/full-screen-loader'
], function(redirectOnSuccessAction, fullScreenLoader) {
   'use strict';

   return {
       execute: function(invId) {
           fullScreenLoader.stopLoader();
           redirectOnSuccessAction.execute();
       }
   }
});
