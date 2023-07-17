require([
    'mage/storage',
    'mage/url',
    'jquery'
], function(storage, urlBuilder, $) {
    $(document).ready(function() {
        let url = urlBuilder.build('/rest/default/V1/wegetfinancing/get-ppe-config')

        storage.get(
            url,
            '',
            true
        ).done(
            function (json) {
                response = JSON.parse(json);

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
                    position: response.position
                };

                window.ppeConfiguration = ppeConfiguration;

                console.log('PPE' + JSON.stringify(ppeConfiguration));
            }
        );
    });
});
