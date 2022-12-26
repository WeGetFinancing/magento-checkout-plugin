<?php

namespace WeGetFinancing\Checkout\Api;

/**
 * @api
 */
interface UpdatePostbackInterface
{
    /**
     * @param string $version
     * @return string
     */
    public function updatePostback(
        string $version,
        string $request_token,
        string $updates,
        string $merchant_transaction_id
    ): string;

}
