<?php

namespace WeGetFinancing\Checkout\Api;

/**
 * @api
 */
interface ValidateCredentialsInterface
{
    /**
     * @param string $token
     * @return string
     */
    public function validateMerchantToken(
        string $token
    ): string;
}
