require([
    'mage/storage',
    'mage/url'
], function(storage, urlBuilder) {
    let url = urlBuilder.build('/rest/default/V1/wegetfinancing/get-ppe-config')

    storage.get(
        url,
        '',
        true
    ).done(
        function (json) {
            let response = JSON.parse(json);

            require([response.ppeJsUrl]);

            const ppeConfiguration = {
                priceSelector: response.priceSelector,
                productNameSelector: response.productNameSelector,
                debug: response.debug,
                token: response.token,
                applyNow: response.applyNow,
                branded: response.branded,
                minAmount: response.minAmount,
                customText: response.customText,
                hover: response.hover,
                fontSize: response.fontSize,
                position: response.position
            };

            window.ppeConfiguration = ppeConfiguration;
        }
    );
});
