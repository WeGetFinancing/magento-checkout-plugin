<?php

namespace WeGetFinancing\Checkout\Service\Http;

use WeGetFinancing\Checkout\Entity\Request\FunnelGeneratorRequest;
use WeGetFinancing\Checkout\Entity\Response\JsonResponse;
use WeGetFinancing\Checkout\Exception\WGFClientException;
use WeGetFinancing\Checkout\Gateway\Config;
use WeGetFinancing\SDK\Entity\AuthEntity;
use WeGetFinancing\SDK\Client as SDKClient;
use WeGetFinancing\SDK\Entity\Request\LoanRequestEntity;
use WeGetFinancing\SDK\Entity\Request\UpdateShippingStatusRequestEntity;
use WeGetFinancing\SDK\Entity\Response\ResponseEntity;
use WeGetFinancing\SDK\Exception\EntityValidationException;
use Psr\Log\LoggerInterface;
use \Throwable;

class WGFClient
{
    public function __construct(
        private Config $config,
        private LoggerInterface $logger
    ) { }

    /**
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

    public function sendShipmentNotification(UpdateShippingStatusRequestEntity $updateEntity): ResponseEntity
    {
        $authEntity = $this->getAuthEntity();
        $client = SDKClient::make($authEntity);
        return $client->updateStatus($updateEntity);
    }

    /**
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
