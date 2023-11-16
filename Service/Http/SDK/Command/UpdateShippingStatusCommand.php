<?php

declare(strict_types=1);

namespace WeGetFinancing\Checkout\Service\Http\SDK\Command;

use WeGetFinancing\Checkout\Service\Http\SDK\Entity\AuthEntity;
use WeGetFinancing\Checkout\Service\Http\SDK\Entity\Request\AbstractRequestEntity;
use WeGetFinancing\Checkout\Service\Http\SDK\Entity\Request\UpdateShippingStatusRequestEntity;
use WeGetFinancing\Checkout\Service\Http\SDK\Entity\Response\ResponseEntity;
use WeGetFinancing\Checkout\Service\Http\SDK\Service\Http\V3\HttpClientV3;

class UpdateShippingStatusCommand extends AbstractCommand
{
    public const UPDATE_SHIPPING_STATUS_VERB = 'POST';
    public const REPLACE_INV_ID_URL = '_INV_ID_';
    public const UPDATE_SHIPPING_STATUS_PATH = '/v3/lead/_INV_ID_/shipping_status';

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @param AuthEntity $authEntity
     * @return UpdateShippingStatusCommand
     */
    public static function make(AuthEntity $authEntity): self
    {
        $client = HttpClientV3::make($authEntity);
        return new UpdateShippingStatusCommand($client);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * @param UpdateShippingStatusRequestEntity $requestEntity
     * @return ResponseEntity
     */
    public function execute(AbstractRequestEntity $requestEntity): ResponseEntity
    {
        return $this->httpClient->request(
            self::UPDATE_SHIPPING_STATUS_VERB,
            $this->getInvIdPath((string)$requestEntity->getInvId()),
            $requestEntity->getWeGetFinancingRequest()
        );
    }

    protected function getInvIdPath(string $invId): string
    {
        return str_replace(
            self::REPLACE_INV_ID_URL,
            $invId,
            self::UPDATE_SHIPPING_STATUS_PATH
        );
    }
}
