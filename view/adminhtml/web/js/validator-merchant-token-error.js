require(
    [
        'mage/url',
        'jquery',
        'mage/translate',
        'jquery/validate'
    ],
    function(urlBuilder, $){
        $.validator.addMethod(
            'validate-wgf-merchant-token-error',
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

                return !('error' === String(ppeEmptyStatus));
            },
            $.mage.__("The PPE token provided is invalid. Please retrieve the correct token by logging into your merchant account within the WeGetFinancing backoffice. For further clarification, refer to the supporting documentation.\n " +
                "Please consult your integration documentation and contact support if problem cannot be rectified.")
        );
    }
);
