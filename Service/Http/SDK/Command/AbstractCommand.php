<?php

declare(strict_types=1);

namespace WeGetFinancing\Checkout\Service\Http\SDK\Command;

use WeGetFinancing\Checkout\Service\Http\SDK\Service\Http\HttpClientInterface;

abstract class AbstractCommand implements CommandInterface
{
    public function __construct(protected HttpClientInterface $httpClient)
    {
    }
}
