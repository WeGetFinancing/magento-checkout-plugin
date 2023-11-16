<?php

declare(strict_types=1);

namespace WeGetFinancing\Checkout\Service\Http\SDK;

use GuzzleHttp\Exception\GuzzleException;
use WeGetFinancing\Checkout\Service\Http\SDK\Command\RequestNewLoanCommand;
use WeGetFinancing\Checkout\Service\Http\SDK\Command\UpdateShippingStatusCommand;
use WeGetFinancing\Checkout\Service\Http\SDK\Entity\AuthEntity;
use WeGetFinancing\Checkout\Service\Http\SDK\Entity\Request\LoanRequestEntity;
use WeGetFinancing\Checkout\Service\Http\SDK\Entity\Request\UpdateShippingStatusRequestEntity;
use WeGetFinancing\Checkout\Service\Http\SDK\Entity\Response\ResponseEntity;
use WeGetFinancing\Checkout\Service\Http\SDK\Exception\EntityValidationException;
use WeGetFinancing\Checkout\Service\Http\SDK\Service\PpeClient;

class Client
{
    public function __construct(
        protected AuthEntity $authEntity
    ) {
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @param AuthEntity $authEntity
     * @return Client
     */
    public static function make(AuthEntity $authEntity): Client
    {
        return new Client($authEntity);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @param LoanRequestEntity $requestEntity
     * @throws EntityValidationException
     * @return ResponseEntity
     */
    public function requestNewLoan(LoanRequestEntity $requestEntity): ResponseEntity
    {
        $command = RequestNewLoanCommand::make($this->authEntity);
        return $command->execute($requestEntity);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @param UpdateShippingStatusRequestEntity $requestEntity
     * @return ResponseEntity
     */
    public function updateStatus(UpdateShippingStatusRequestEntity $requestEntity): ResponseEntity
    {
        $command = UpdateShippingStatusCommand::make($this->authEntity);
        return $command->execute($requestEntity);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @param string $merchantToken
     * @throws GuzzleException
     * @return array<string, string>
     */
    public function testPpe(string $merchantToken): array
    {
        $command = PpeClient::make($merchantToken, $this->authEntity->isProd());
        return $command->testPpe();
    }
}
