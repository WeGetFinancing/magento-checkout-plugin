<?php

namespace WeGetFinancing\Checkout\Api;

/**
 * @api
 */
interface PostbackUrlInterface
{
    /**
     * @param string $request
     * @return string
     */
    public function postbackUrl(string $request): string;

}
