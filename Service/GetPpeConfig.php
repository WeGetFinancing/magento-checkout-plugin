<?php

namespace WeGetFinancing\Checkout\Service;

use Magento\Framework\Exception\CouldNotSaveException;
use Psr\Log\LoggerInterface;
use WeGetFinancing\Checkout\Api\GetPpeConfigInterface;
use Throwable;
use WeGetFinancing\Checkout\Gateway\Config;

class GetPpeConfig implements GetPpeConfigInterface
{
    use JsonStringifyResponseTrait;

    /**
     * GetPpeConfig constructor.
     *
     * @param LoggerInterface $logger
     * @param Config $config
     */
    public function __construct(
        private LoggerInterface $logger,
        private Config          $config
    ) {
    }

    /**
     * Get PPE Config
     *
     * @return string
     * @throws CouldNotSaveException
     */
    public function getPpeConfig(): string
    {
        try {
            return $this->jsonStringifyResponse([
                'priceSelector' => $this->config->getPpePriceSelector(),
                'productNameSelector' => $this->config->getPpeProductNameSelector(),
                'debug' => $this->config->getPpeIsDebug(),
                'token' => $this->config->getPpeToken(),
                'applyNow' => $this->config->getPpeIsApplyNow(),
                'branded' => $this->config->getPpeIsBranded(),
                'minAmount' => $this->config->getPpeMinAmount(),
                'customText' => $this->config->getPpeCustomText(),
                'position' => $this->config->getPpePosition(),
                'hover' => $this->config->getPpeIsHover(),
                'fontSize' => $this->config->getPpeFontSize(),
                'ppeJsUrl' => $this->config->getPpeJsUrl()
            ]);
        } catch (Throwable $exception) {
            $this->logger->error(self::class . '::getPpeConfig() Exception');
            $this->logger->error($exception);
        }

        return $this->jsonStringifyResponse([
            'status' => 'error',
            'message' => 'unexpected server error'
        ]);
    }
}
