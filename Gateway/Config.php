<?php

namespace WeGetFinancing\Checkout\Gateway;

use Magento\Framework\Exception\NotFoundException;
use Magento\Payment\Gateway\Config\ValueHandlerPoolInterface;

class Config
{
    private ValueHandlerPoolInterface $valueHandlerPool;

    /**
     * Config constructor.
     * @param ValueHandlerPoolInterface $valueHandlerPool
     */
    public function __construct(
        ValueHandlerPoolInterface $valueHandlerPool
    ) {
        $this->valueHandlerPool = $valueHandlerPool;
    }

    public function isSandbox(): bool
    {
        return (bool) $this->getValue('is_sandbox');
    }

    /**
     * @return string
     */
    public function getSdkUrl()
    {
        return (string) $this->getValue('sdk_url');
    }

    /**
     * @return string
     */
    public function getPaymentCardSrc()
    {
        return (string) $this->getValue('payment_card_src');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return (string) $this->getValue('title');
    }

    /**
     * @return string
     */
    public function getMerchantSourceId()
    {
        return (string) $this->getValue('merchant_source_id');
    }

    /**
     * @return string
     */
    public function getReviewMessage()
    {
        return (string) $this->getValue('review_message');
    }

    /**
     * @return string
     */
    public function getButtonActionTitle()
    {
        return (string) $this->getValue('button_action_title');
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return (string) $this->getValue('display_name');
    }

    /**
     * @return bool
     */
    public function isCollectShipping()
    {
        return (bool) $this->getValue('is_collect_shipping');
    }

    public function getWeGetFinancingUsername(): string
    {
        return (string) $this->getValue('wegetfinancing_username');
    }

    public function getWeGetFinancingPassword(): string
    {
        return (string) $this->getValue('wegetfinancing_password');
    }

    public function getWeGetFinancingMerchantId(): string
    {
        return (string) $this->getValue('wegetfinancing_merchant_id');
    }

    public function isWeGetFinancingSandbox(): bool
    {
        return (bool) $this->getValue('is_wegetfinancing_sandbox');
    }

    public function getWeGetFinancingUrl(): string
    {
        return ($this->isWeGetFinancingSandbox())
            ? (string) $this->getValue('wegetfinancing_url_sandbox')
            : (string) $this->getValue('wegetfinancing_url');
    }

    public function getWeGetFinancingPostBackUrl(): string
    {
        return (string) $this->getValue('wegetfinancing_postback_url');
    }

    public function getWeGetFinancingVersion(): string
    {
        return (string) $this->getValue('wegetfinancing_version');
    }

    /**
     * @param string $field
     * @return mixed|null
     */
    private function getValue(string $field)
    {
        try {
            $handler = $this->valueHandlerPool->get($field);

            return $handler->handle(['field' => $field]);
        } catch (NotFoundException $exception) {
            return null;
        }
    }
}
