<?php

namespace WeGetFinancing\Checkout\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use WeGetFinancing\Checkout\Gateway\Config;
use Magento\Quote\Model\Quote;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Repository
     */
    private $assetRepo;

    /**
     * @var Quote
     */
    private $quote;

    /**
     * ConfigProvider constructor.
     * @param Config $config
     * @param UrlInterface $url
     * @param Repository $repository
     * @param RequestInterface $request
     * @param Session $session
     */
    public function __construct(
        Config $config,
        UrlInterface $url,
        Repository $repository,
        RequestInterface $request,
        Session $session
    ) {
        $this->config = $config;
        $this->url = $url;
        $this->assetRepo = $repository;
        $this->request = $request;
        $this->quote = $session->getQuote();
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                'wegetfinancing_payment' => [
                    'title' => $this->config->getTitle(),
                    'sdkUrl' => $this->config->getSdkUrl(),
                    'paymentCardSrc' => $this->config->getPaymentCardSrc(),
                ]
            ]
        ];
    }

    /**
     * @return string
     */
    private function getLogoUrl()
    {
        return $this->getViewFileUrl('images/logo.svg');
    }

    /**
     * @param string $fileId
     * @return string
     */
    public function getViewFileUrl($fileId)
    {
        try {
            $params = array_merge(['_secure' => $this->request->isSecure()]);
            return $this->assetRepo->getUrlWithParams($fileId, $params);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return '';
        }
    }
}
