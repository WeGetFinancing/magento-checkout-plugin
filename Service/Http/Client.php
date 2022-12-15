<?php

namespace WeGetFinancing\Checkout\Service\Http;

use GuzzleHttp\Exception\GuzzleException;
use WeGetFinancing\Checkout\Entity\ErrorEntity;
use WeGetFinancing\Checkout\Entity\Request\FunnelGeneratorRequest;
use WeGetFinancing\Checkout\Entity\Response\JsonResponse;
use WeGetFinancing\Checkout\Exception\ClientException;
use WeGetFinancing\Checkout\Gateway\Config;
use WeGetFinancing\SDK\Entity\Request\AuthRequestEntity;
use WeGetFinancing\SDK\Client as SDKClient;
use WeGetFinancing\SDK\Entity\Request\LoanRequestEntity;
use WeGetFinancing\SDK\Exception\EntityValidationException;


class Client
{
    private Config $config;

    public function __construct(Config $config) {
        $this->config = $config;
    }

    /**
     * @param FunnelGeneratorRequest $funnelGeneratorRequest
     * @return JsonResponse
     * @throws GuzzleException
     * @throws EntityValidationException
     */
    public function execute(FunnelGeneratorRequest $funnelGeneratorRequest): JsonResponse
    {

        $auth = AuthRequestEntity::make([
            'username' => $this->config->getWeGetFinancingUsername(),
            'password' => $this->config->getWeGetFinancingPassword(),
            'merchantId' => $this->config->getWeGetFinancingMerchantId(),
            'url' => $this->config->getWeGetFinancingUrl()
        ]);

        $loanRequest = LoanRequestEntity::make($funnelGeneratorRequest->toArray());

        $loanResponse = SDKClient::make($auth)->requestNewLoan($loanRequest);

        if (true === $loanResponse->getIsSuccess()) {
            return new JsonResponse([
                'type' => JsonResponse::TYPE_SUCCESS,
                'data' => [
                    'href' => $loanResponse->getSuccess()->getHref(),
                    'inv_id' => $loanResponse->getSuccess()->getInvId()
                ]
            ]);
        }

        $error = $loanResponse->getError();
        $violations = [];

        $violations[] = (new ErrorEntity([
            'field' => 'unknown',
            'message' => $error->getMessage()
        ]))->toArray();

        foreach ($error->getReasons() as $reason) {
            $violations[] = (new ErrorEntity([
                'field' => 'unknown',
                'message' => $reason
            ]))->toArray();
        }

        throw new ClientException(
            ClientException::SERVER_RESPONSE_ERROR_MESSAGE,
            ClientException::SERVER_RESPONSE_ERROR_CODE,
            null,
            $violations
        );
    }
}
