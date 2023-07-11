<?php

namespace WeGetFinancing\Checkout\Gateway;

use Magento\Framework\Exception\NotFoundException;
use Magento\Payment\Gateway\Config\ValueHandlerPoolInterface;
use Magento\Framework\UrlInterface;

class Config
{
    /**
     * Config constructor.
     * @param ValueHandlerPoolInterface $valueHandlerPool
     * @param UrlInterface $url
     */
    public function __construct(
        private ValueHandlerPoolInterface $valueHandlerPool,
        private UrlInterface $url
    ) { }

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

    public function isProd(): bool
    {
        return false === (bool) $this->getValue('is_sandbox');
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
        return $this->url->getBaseUrl() . $this->getValue('api_magento_rest_path') . $this->getPostBackPath();
    }

    public function getApiVersion(): string
    {
        return (string) $this->getValue('api_version');
    }

    public function getPpeJsUrl(): string
    {
        return $this->isProd()
            ? (string) $this->getValue('ppe_js_prod')
            : (string) $this->getValue('ppe_js_sandbox');
    }

    public function getPpePriceSelector(): string
    {
        return (string) $this->getValue('ppe_price_selector');
    }

    public function getPpeProductNameSelector(): string
    {
        return (string) $this->getValue('ppe_product_name_selector');
    }

    public function getPpeIsDebug(): bool
    {
        return (bool) $this->getValue('ppe_is_debug');
    }

    public function getPpeToken(): string
    {
        return (string) $this->getValue('ppe_merchant_token');
    }

    public function getPpeIsApplyNow(): bool
    {
        return (bool) $this->getValue('ppe_is_apply_now');
    }

    public function getPpeIsBranded(): bool
    {
        return (bool) $this->getValue('ppe_is_branded');
    }

    public function getPpeMinAmount(): string
    {
        return (string) $this->getValue('ppe_minimum_amount');
    }

    public function getPpeCustomText(): string
    {
        return (string) $this->getValue('ppe_custom_text');
    }

    public function getPpePosition(): string
    {
        return (string) $this->getValue('ppe_position');
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
