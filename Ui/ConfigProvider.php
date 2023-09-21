<?php

namespace WeGetFinancing\Checkout\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Asset\Repository;
use WeGetFinancing\Checkout\Gateway\Config;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * ConfigProvider constructor.
     *
     * @param Config $config
     * @param Repository $assetRepo
     * @param RequestInterface $request
     */
    public function __construct(
        private Config $config,
        private Repository $assetRepo,
        private RequestInterface $request,
    ) {
    }

    /**
     * Get Config
     *
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
                    'cartPath' => $this->config->getCartPath()
                ]
            ]
        ];
    }

    /**
     * Get Logo Url
     *
     * @return string
     */
    private function getLogoUrl(): string
    {
        return $this->getViewFileUrl('images/logo.svg');
    }

    /**
     * Get View File Url
     *
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
