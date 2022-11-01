<?php

namespace WeGetFinancing\Checkout\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use WeGetFinancing\Checkout\Gateway\Config;

class DecryptBuilder implements BuilderInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * DecryptBuilder constructor.
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $response = $buildSubject['response'];

        $dataValue = $response['encPaymentData'] ?? null;
        $dataKey = $response['encKey'] ?? null;
        $transactionId = $response['callid'] ?? null;
        return [
            'decryptPaymentDataRequest' => [
                'merchantAuthentication' => [
                    'name' => '',
                    'transactionKey' => ''
                ],
                'opaqueData' => [
                    'dataDescriptor' => 'COMMON.VCO.ONLINE.PAYMENT',
                    'dataValue' => $dataValue,
                    'dataKey' => $dataKey
                ],
                'callId' => $transactionId
            ]
        ];
    }
}
