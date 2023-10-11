<?php

namespace WeGetFinancing\Checkout\Api;

/**
 * @api
 */
interface GetPpeConfigInterface
{
    /**
     * Get PPE Config
     *
     * @return string
     */
    public function getPpeConfig(): string;
}
