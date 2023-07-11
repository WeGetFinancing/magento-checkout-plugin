<?php

namespace WeGetFinancing\Checkout\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use WeGetFinancing\Checkout\Gateway\Config;
use Magento\Quote\Model\Quote;

class ConfigProvider implements ConfigProviderInterface
{
    private Quote $quote;

    /**
     * ConfigProvider constructor.
     * @param Config $config
     * @param UrlInterface $url
     * @param Repository $assetRepo
     * @param RequestInterface $request
     * @param Session $session
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function __construct(
        private Config $config,
        private UrlInterface $url,
        private Repository $assetRepo,
        private RequestInterface $request,
        Session $session
    ) {
        $this->quote = $session->getQuote();
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'payment' => [
                'wegetfinancing_payment' => [
                    'title' => $this->config->getTitle(),
                    'sdkUrl' => $this->config->getSdkUrl(),
                    'paymentIconUrl' => $this->config->getPaymentIconUrl(),
                    'cartGuestPath' => $this->config->getCartGuestPath(),
                    'cartPath' => $this->config->getCartPath(),
                    'ppeJsUrl' => $this->config-getPpeJsUrl(),
                    'ppePriceSelector' => $this->config-getPpePriceSelector(),
                    'ppeProductNameSelector' => $this->config-getPpeProductNameSelector(),
                    'ppeIsDebug' => $this->config-getPpeIsDebug(),
                    'ppeToken' => $this->config-getPpeToken(),
                    'ppeIsApplyNow' => $this->config-getPpeIsApplyNow(),
                    'ppeIsBranded' => $this->config-getPpeIsBranded(),
                    'ppeMinAmount' => $this->config-getPpeMinAmount(),
                    'ppeCustomText' => $this->config-getPpeCustomText(),
                    'ppePosition' => $this->config-getPpePosition()
                ]
            ]
        ];
    }

    /**
     * @return string
     */
    private function getLogoUrl(): string
    {
        return $this->getViewFileUrl('images/logo.svg');
    }

    /**
     * @param string $fileId
     * @return string
     */
    public function getViewFileUrl(string $fileId): string
    {
        try {
            $params = array_merge(['_secure' => $this->request->isSecure()]);
            return $this->assetRepo->getUrlWithParams($fileId, $params);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return '';
        }
    }
}
