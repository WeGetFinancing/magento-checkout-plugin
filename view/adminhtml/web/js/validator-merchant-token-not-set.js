require(
    [
        'jquery',
        'mage/translate',
        'jquery/validate'
    ],
    function($){
        $.validator.addMethod(
            'validate-wgf-merchant-token-not-set',
            function (value) {
                return !("" === String(value).trim());
            },
            $.mage.__("The PPE Merchant Token cannot be empty.")
        );
    }
);
