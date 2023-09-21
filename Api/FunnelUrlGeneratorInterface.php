<?php

namespace WeGetFinancing\Checkout\Api;

/**
 * @api
 */
interface FunnelUrlGeneratorInterface
{
    /**
     * Generate funnel url for public user
     *
     * @param string $request
     * @return string
     */
    public function generateFunnelUrlPublic(string $request): string;

    /**
     * Generate funnel url for registered user
     *
     * @param string $request
     * @return string
     */
    public function generateFunnelUrlRegistered(string $request): string;
}
