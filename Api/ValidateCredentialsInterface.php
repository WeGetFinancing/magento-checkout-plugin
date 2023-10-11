<?php

namespace WeGetFinancing\Checkout\Api;

/**
 * @api
 */
interface ValidateCredentialsInterface
{
    /**
     * Validate Merchant Token
     *
     * @param string $token
     * @return string
     */
    public function validateMerchantToken(
        string $token
    ): string;
}
