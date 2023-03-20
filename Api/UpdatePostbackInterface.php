<?php

namespace WeGetFinancing\Checkout\Api;

/**
 * @api
 */
interface UpdatePostbackInterface
{
    /**
     * @param string $version
     * @param string $request_token
     * @param mixed $updates
     * @param string $merchant_transaction_id
     * @return string
     */
    public function updatePostback(
        string $version,
        string $request_token,
        mixed $updates,
        string $merchant_transaction_id
    ): string;

}
