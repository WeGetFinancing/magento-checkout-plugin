<?php

namespace WeGetFinancing\Checkout\Api;

/**
 * @api
 */
interface FunnelUrlGeneratorInterface
{
    /**
     * @param string $request
     * @return string
     */
    public function generateFunnelUrlPublic(string $request): string;

    /**
     * @param string $request
     * @return string
     */
    public function generateFunnelUrlRegistered(string $request): string;
}
