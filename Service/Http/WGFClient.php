<?php

namespace WeGetFinancing\Checkout\Service\Http;

use GuzzleHttp\Exception\GuzzleException;
use WeGetFinancing\Checkout\Entity\Request\FunnelGeneratorRequest;
use WeGetFinancing\Checkout\Entity\Response\JsonResponse;
use WeGetFinancing\Checkout\Exception\WGFClientException;
use WeGetFinancing\Checkout\Gateway\Config;
use WeGetFinancing\Checkout\ValueObject\PpeSettings;
use WeGetFinancing\SDK\Entity\AuthEntity;
use WeGetFinancing\SDK\Client as SDKClient;
use WeGetFinancing\SDK\Entity\Request\LoanRequestEntity;
use WeGetFinancing\SDK\Entity\Request\UpdateShippingStatusRequestEntity;
use WeGetFinancing\SDK\Entity\Response\ResponseEntity;
use WeGetFinancing\SDK\Exception\EntityValidationException;
use Psr\Log\LoggerInterface;
use \Throwable;
use WeGetFinancing\SDK\Service\PpeClient;

class WGFClient
{
    public const PPE_TEST_SUCCESS = "Merchant Token is Valid";

    /**
     * WGFClient __construct
     *
     * @param Config $config
     * @param LoggerInterface $logger
     */
    public function __construct(
        private Config $config,
        private LoggerInterface $logger
    ) {
    }

    /**
     * Get funnel url
     *
     * @param FunnelGeneratorRequest $funnelGeneratorRequest
     * @return JsonResponse
     * @throws EntityValidationException|WGFClientException
     */
    public function getFunnel(FunnelGeneratorRequest $funnelGeneratorRequest): JsonResponse
    {
        $authEntity = $this->getAuthEntity();

        $client = SDKClient::make($authEntity);

        $loanRequest = LoanRequestEntity::make($funnelGeneratorRequest->toArray());

        $loanResponse = $client->requestNewLoan($loanRequest);

        $data = $loanResponse->getData();

        if (true === $loanResponse->getIsSuccess()) {
            return new JsonResponse([
                'type' => JsonResponse::TYPE_SUCCESS,
                'data' => [
                    'href' => $data['href'],
                    'inv_id' => $data['invId']
                ]
            ]);
        }

        $this->logger->error(self::class . '::getFunnel(): Malformed request');
        $this->logger->error('Response data: ' . json_encode($data));

        throw new WGFClientException(
            WGFClientException::SERVER_RESPONSE_ERROR_MESSAGE,
            WGFClientException::SERVER_RESPONSE_ERROR_CODE,
        );
    }

    /**
     * Send shipment notification
     *
     * @param UpdateShippingStatusRequestEntity $updateEntity
     * @return ResponseEntity
     * @throws WGFClientException
     */
    public function sendShipmentNotification(UpdateShippingStatusRequestEntity $updateEntity): ResponseEntity
    {
        $authEntity = $this->getAuthEntity();
        $client = SDKClient::make($authEntity);
        return $client->updateStatus($updateEntity);
    }

    /**
     * Validate PPE Merchant Token
     *
     * @param string $token
     * @return array<string, string>
     * @throws WGFClientException
     * @throws GuzzleException
     */
    public function validatePpeMerchantToken(string $token): array
    {
        $authEntity = AuthEntity::make([
            'username' => 'not-applicable',
            'password' => 'not-applicable',
            'merchantId' => 'not-applicable',
            'prod' => $this->config->isProd()
        ]);
        $client = SDKClient::make($authEntity);
        return $client->testPpe($token);
    }

    /**
     * Get Auth Entity
     *
     * @return AuthEntity
     * @throws WGFClientException
     */
    protected function getAuthEntity(): AuthEntity
    {
        try {
            return AuthEntity::make([
                'username' => $this->config->getUsername(),
                'password' => $this->config->getPassword(),
                'merchantId' => $this->config->getMerchantId(),
                'prod' => $this->config->isProd()
            ]);
        } catch (EntityValidationException $exception) {
            $this->logger->error(self::class . '::getAuthEntity(): Malformed auth entity');
            $this->logger->error($exception->getCode() . ' ' . $exception->getMessage());
            foreach ($exception->getViolations() as $violation) {
                $this->logger->error($violation);
            }
        } catch (Throwable $exception) {
            $this->logger->critical(self::class . '::getAuthEntity(): Malformed auth entity');
            $this->logger->critical($exception->getCode() . ' ' . $exception->getMessage(), $exception->getTrace());
        }

        throw new WGFClientException(
            WGFClientException::MALFORMED_AUTH_ENTITY_MESSAGE,
            WGFClientException::MALFORMED_AUTH_ENTITY_CODE
        );
    }
}
