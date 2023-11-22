<?php

namespace WeGetFinancing\Checkout\Observer;

use DateTime;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Shipment;
use Psr\Log\LoggerInterface;
use Throwable;
use WeGetFinancing\Checkout\Exception\SalesOrderShipmentAfterException;
use WeGetFinancing\Checkout\Model\WeGetFinancingTransactionFactory;
use WeGetFinancing\Checkout\Model\ResourceModel\WeGetFinancingTransaction as WGFTransactionResource;
use WeGetFinancing\Checkout\Model\ResourceModel\WeGetFinancingTransaction\Collection as WGFTransactionCollection;
use WeGetFinancing\Checkout\Service\Http\WGFClient;
use WeGetFinancing\SDK\Entity\Request\UpdateShippingStatusRequestEntity;

class SalesOrderShipmentAfter implements ObserverInterface
{
    /**
     * SalesOrderShipmentAfter __construct
     *
     * @param LoggerInterface $logger
     * @param WGFClient $wGFClient
     * @param WeGetFinancingTransactionFactory $transactionFactory
     * @param WGFTransactionResource $transactionResource
     * @param WGFTransactionCollection $transactionCollection
     */
    public function __construct(
        private LoggerInterface                  $logger,
        private WGFClient                        $wGFClient,
        private WeGetFinancingTransactionFactory $transactionFactory,
        private WGFTransactionResource           $transactionResource,
        private WGFTransactionCollection         $transactionCollection
    ) {
    }

    /**
     * Execute the main purpose of this class
     *
     * @param  Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        try {
            /** @var Shipment $shipment */
            $shipment = $observer->getEvent()->getShipment();
            $order = $shipment->getOrder();
            $quoteId = $order->getQuoteId();
            $this->transactionCollection->addFilter('order_id', $quoteId);
            $found = $this->transactionCollection->getData();

            if (true === empty($found)) {
                return;
            }

            if (count($found) >= 2) {
                throw new SalesOrderShipmentAfterException(
                    str_replace(
                        SalesOrderShipmentAfterException::ORDER_ID_REPLACE,
                        $quoteId,
                        SalesOrderShipmentAfterException::MULTIPLE_TRANSACTIONS_IN_DB_MESSAGE
                    ),
                    SalesOrderShipmentAfterException::MULTIPLE_TRANSACTIONS_IN_DB_CODE
                );
            }

            $transactionArray = array_pop($found);
            if (true === (bool)$transactionArray['shipment_notification_sent']) {
                return;
            }

            $trackNumber = $carrierName = '-';
            $tracksCollection = $shipment->getTracksCollection();
            foreach ($tracksCollection->getItems() as $track) {
                $trackNumber = $track->getTrackNumber();
                $carrierName = $track->getTitle();
            }
            $updateRequest = UpdateShippingStatusRequestEntity::make([
                'shippingStatus' => UpdateShippingStatusRequestEntity::STATUS_SHIPPED,
                'trackingId' => $trackNumber,
                'trackingCompany' => $carrierName,
                'deliveryDate' => (new DateTime())->modify('+1 day')->format('Y-m-d'),
                'invId' => $transactionArray['inv_id']
            ]);

            $response = $this->wGFClient->sendShipmentNotification($updateRequest);

            if (true === $response->getIsSuccess()) {
                $transactionArray['shipment_notification_sent'] = true;
                $transaction = $this->transactionFactory->create();
                $transaction->setData($transactionArray);
                $this->transactionResource->save($transaction);
                return;
            }

            $this->logger->error(self::class . '::execute() Error');
            $this->logger->error('Response code: ' . $response->getCode());
            $this->logger->error('Response data: ' . json_encode($response->getData()));
        } catch (Throwable $exception) {
            $this->logger->error(self::class . '::execute() Exception');
            $this->logger->error($exception);
        }
    }
}
