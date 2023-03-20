<?php

namespace WeGetFinancing\Checkout\Webapi\Rest\Response\Renderer;

use Magento\Framework\Webapi\Rest\Response\RendererInterface;

class Text implements RendererInterface
{
    const MIME_TYPE = 'text/plain';

    /**
     * Convert data to JSON.
     *
     * @param object|array|int|string|bool|float|null $data
     * @return string
     */
    public function render($data): string
    {
        return (string) $data;
    }

    /**
     * Get JSON renderer MIME type.
     *
     * @return string
     */
    public function getMimeType(): string
    {
        return self::MIME_TYPE;
    }
}

