require([], function () {
    const config = window.checkoutConfig.payment.wegetfinancing_payment;

    require([config.ppeJsUrl]);

    window.ppeConfiguration = {
        priceSelector: config.ppePriceSelector,
        productNameSelector: config.ppeProductNameSelector,
        debug: config.ppeIsDebug,
        token: config.ppeToken,
        applyNow: config.ppeIsApplyNow,
        branded: config.ppeIsBranded,
        minAmount: config.ppeMinAmount,
        customText: config.ppeCustomText,
        position: config.ppePosition
    };
});
