<?php

namespace WeGetFinancing\Checkout\Gateway;

use Magento\Framework\Exception\NotFoundException;
use Magento\Payment\Gateway\Config\ValueHandlerPoolInterface;
use Magento\Framework\UrlInterface;

class Config
{
    /**
     * Config constructor.
     *
     * @param ValueHandlerPoolInterface $valueHandlerPool
     * @param UrlInterface $url
     */
    public function __construct(
        private ValueHandlerPoolInterface $valueHandlerPool,
        private UrlInterface $url
    ) {
    }

    /**
     * Get SDK Url
     *
     * @return string
     */
    public function getSdkUrl(): string
    {
        return (string) $this->getValue('sdk_url');
    }

    /**
     * Get Payment Icon Url
     *
     * @return string
     */
    public function getPaymentIconUrl(): string
    {
        return (string) $this->getValue('payment_icon_url');
    }

    /**
     * Get Title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return (string) $this->getValue('title');
    }

    /**
     * Get Button Action Title
     *
     * @return string
     */
    public function getButtonActionTitle(): string
    {
        return (string) $this->getValue('button_action_title');
    }

    /**
     * Get Display Name
     *
     * @return string
     */
    public function getDisplayName(): string
    {
        return (string) $this->getValue('display_name');
    }

    /**
     * Is Collect Shipping
     *
     * @return bool
     */
    public function isCollectShipping(): bool
    {
        return (bool) $this->getValue('is_collect_shipping');
    }

    /**
     * Get Username
     *
     * @return string
     */
    public function getUsername(): string
    {
        return (string) $this->getValue('username');
    }

    /**
     * Get Password
     *
     * @return string
     */
    public function getPassword(): string
    {
        return (string) $this->getValue('password');
    }

    /**
     * Get Merchant Id
     *
     * @return string
     */
    public function getMerchantId(): string
    {
        return (string) $this->getValue('merchant_id');
    }

    /**
     * Is Prod
     *
     * @return bool
     */
    public function isProd(): bool
    {
        return false === (bool) $this->getValue('is_sandbox');
    }

    /**
     * Get Cart Guest Path
     *
     * @return string
     */
    public function getCartGuestPath(): string
    {
        return (string) $this->getValue('cart_guest_path');
    }

    /**
     * Get Cart Registered Path
     *
     * @return string
     */
    public function getCartPath(): string
    {
        return (string) $this->getValue('cart_path');
    }

    /**
     * Get Postback Path
     *
     * @return string
     */
    public function getPostBackPath(): string
    {
        return (string) $this->getValue('postback_path');
    }

    /**
     * Get Postback Url
     *
     * @return string
     */
    public function getPostBackUrl(): string
    {
        return $this->url->getBaseUrl() . $this->getValue('api_magento_rest_path') . $this->getPostBackPath();
    }

    /**
     * Get Api Version
     *
     * @return string
     */
    public function getApiVersion(): string
    {
        return (string) $this->getValue('api_version');
    }

    /**
     * Get PPE JS Url
     *
     * @return string
     */
    public function getPpeJsUrl(): string
    {
        return $this->isProd()
            ? (string) $this->getValue('ppe_js_prod')
            : (string) $this->getValue('ppe_js_sandbox');
    }

    /**
     * Get PPE Price Selector
     *
     * @return string
     */
    public function getPpePriceSelector(): string
    {
        return (string) $this->getValue('ppe_price_selector');
    }

    /**
     * Get PPE Product Name Selector
     *
     * @return string
     */
    public function getPpeProductNameSelector(): string
    {
        return (string) $this->getValue('ppe_product_name_selector');
    }

    /**
     * Get PPE Is Debug
     *
     * @return bool
     */
    public function getPpeIsDebug(): bool
    {
        return (bool) $this->getValue('ppe_is_debug');
    }

    /**
     * Get PPE Token
     *
     * @return string
     */
    public function getPpeToken(): string
    {
        return (string) $this->getValue('ppe_merchant_token');
    }

    /**
     * Get PPE Is Apply Now
     *
     * @return bool
     */
    public function getPpeIsApplyNow(): bool
    {
        return (bool) $this->getValue('ppe_is_apply_now');
    }

    /**
     * Get PPE Is Branded
     *
     * @return bool
     */
    public function getPpeIsBranded(): bool
    {
        return (bool) $this->getValue('ppe_is_branded');
    }

    /**
     * Get PPE Min Amount
     *
     * @return string
     */
    public function getPpeMinAmount(): string
    {
        return (string) $this->getValue('ppe_minimum_amount');
    }

    /**
     * Get PPE Custom Text
     *
     * @return string
     */
    public function getPpeCustomText(): string
    {
        return (string) $this->getValue('ppe_custom_text');
    }

    /**
     * Get PPE Is Hover
     *
     * @return bool
     */
    public function getPpeIsHover(): bool
    {
        return (bool) $this->getValue('ppe_is_hover');
    }

    /**
     * Get PPE Font Size
     *
     * @return string
     */
    public function getPpeFontSize(): string
    {
        return (string) $this->getValue('ppe_font_size');
    }

    /**
     * Get PPE Position
     *
     * @return string
     */
    public function getPpePosition(): string
    {
        return (string) $this->getValue('ppe_position');
    }

    /**
     * Get Value
     *
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
