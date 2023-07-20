require(
    [
        'mage/url',
        'jquery',
        'mage/translate',
        'jquery/validate'
    ],
    function(urlBuilder, $){
        $.validator.addMethod(
            'validate-wgf-merchant-token-empty',
            function (value) {
                var ppeEmptyStatus = '';

                let url = urlBuilder.build('/rest/default/V1/wegetfinancing/validate-ppe-merchant-token'),
                    payload = {
                        token: value
                    };

                jQuery.ajax({
                    url: url,
                    method: 'post',
                    data: JSON.stringify(payload),
                    contentType: 'application/json; charset=UTF-8',
                    showLoader: false,
                    async: false,
                    beforeSend: function(xhr){}
                }).done(function (json) {
                    let response = JSON.parse(json);
                    ppeEmptyStatus = response.status;
                }).fail(function () {
                    return true;
                });

                return !('empty' === String(ppeEmptyStatus));
            },
            $.mage.__("The merchant account you've selected isn't properly configured for PPE use yet. " +
                "Please reach out to our support team to get your account set up correctly.")
        );
    }
);
