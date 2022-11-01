<?php

namespace WeGetFinancing\Checkout\Common;

use WeGetFinancing\Checkout\Api\ConfigInterface;
use WeGetFinancing\Checkout\Gateway\Config as GatewayConfig;

class Config implements ConfigInterface
{
    /**
     * @var GatewayConfig
     */
    private GatewayConfig $config;

    /**
     * Config constructor.
     * @param GatewayConfig $config
     */
    public function __construct(GatewayConfig $config)
    {
        $this->config = $config;
    }
}
