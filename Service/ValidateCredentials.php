<?php

namespace WeGetFinancing\Checkout\Service;

use Psr\Log\LoggerInterface;
use WeGetFinancing\Checkout\Service\Http\WGFClient;
use Throwable;
use WeGetFinancing\Checkout\Api\ValidateCredentialsInterface;
use WeGetFinancing\Checkout\Gateway\Config;

class ValidateCredentials implements ValidateCredentialsInterface
{
    use JsonStringifyResponseTrait;

    /**
     * UpdatePostback constructor.
     * @param LoggerInterface $logger
     * @param WGFClient $client
     * @param Config $config
     */
    public function __construct(
        private LoggerInterface $logger,
        private WGFClient       $client,
        private Config          $config
    ) { }

    public function validateMerchantToken(string $token): string
    {
        try {
            return $this->jsonStringifyResponse(
                $this->client->validatePpeMerchantToken($token)
            );
        } catch (Throwable $exception) {
            $this->logger->error(self::class . '::updatePostback() Exception');
            $this->logger->error($exception);
        }
        return $this->jsonStringifyResponse([
            'status' => 'critical',
            'message' => 'Internal Server Error'

        ]);
    }
}
