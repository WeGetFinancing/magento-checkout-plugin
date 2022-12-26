<?php

namespace WeGetFinancing\Checkout\Gateway;

use Magento\Framework\Exception\NotFoundException;
use Magento\Payment\Gateway\Config\ValueHandlerPoolInterface;
use Magento\Framework\UrlInterface;
class Config
{
    private ValueHandlerPoolInterface $valueHandlerPool;

    private UrlInterface $url;

    /**
     * Config constructor.
     * @param ValueHandlerPoolInterface $valueHandlerPool
     * @param UrlInterface $url
     */
    public function __construct(
        ValueHandlerPoolInterface $valueHandlerPool,
        UrlInterface $url
    ) {
        $this->valueHandlerPool = $valueHandlerPool;
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getSdkUrl(): string
    {
        return (string) $this->getValue('sdk_url');
    }

    /**
     * @return string
     */
    public function getPaymentIconUrl(): string
    {
        return (string) $this->getValue('payment_icon_url');
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return (string) $this->getValue('title');
    }

    /**
     * @return string
     */
    public function getButtonActionTitle(): string
    {
        return (string) $this->getValue('button_action_title');
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return (string) $this->getValue('display_name');
    }

    /**
     * @return bool
     */
    public function isCollectShipping(): bool
    {
        return (bool) $this->getValue('is_collect_shipping');
    }

    public function getUsername(): string
    {
        return (string) $this->getValue('username');
    }

    public function getPassword(): string
    {
        return (string) $this->getValue('password');
    }

    public function getMerchantId(): string
    {
        return (string) $this->getValue('merchant_id');
    }

    public function isSandbox(): bool
    {
        return (bool) $this->getValue('is_sandbox');
    }

    public function getApiUrl(): string
    {
        return ($this->isSandbox())
            ? (string) $this->getValue('api_sandbox_url')
            : (string) $this->getValue('api_url');
    }

    public function getCartGuestPath(): string
    {
        return (string) $this->getValue('cart_guest_path');
    }

    public function getCartPath(): string
    {
        return (string) $this->getValue('cart_path');
    }

    public function getPostBackPath(): string
    {
        return (string) $this->getValue('postback_path');
    }

    public function getPostBackUrl(): string
    {
        return $this->url->getBaseUrl() . $this->getValue('postback_url') . $this->getPostBackPath();
    }

    public function getOrderToInvIdPath(): string
    {
        return (string) $this->getValue('order_to_inv_id_path');
    }

    public function getApiVersion(): string
    {
        return (string) $this->getValue('api_version');
    }

    /**
     * @param string $field
     * @return mixed|null
     */
    private function getValue(string $field): mixed
    {
        try {
            $handler = $this->valueHandlerPool->get($field);

            return $handler->handle(['field' => $field]);
        } catch (NotFoundException $exception) {
            return null;
        }
    }
}
