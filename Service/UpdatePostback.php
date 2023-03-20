<?php

namespace WeGetFinancing\Checkout\Service;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;
use Magento\Quote\Model\QuoteRepository;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\Order\CreditmemoFactory;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Service\CreditmemoService;
use Psr\Log\LoggerInterface;
use WeGetFinancing\Checkout\Api\UpdatePostbackInterface;
use Throwable;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use WeGetFinancing\Checkout\ValueObject\WgfOrderStatusInterface;
use WeGetFinancing\Checkout\Gateway\Config;
use WeGetFinancing\Checkout\Exception\UpdatePostbackException;
use WeGetFinancing\Checkout\Model\WeGetFinancingTransactionFactory;
use WeGetFinancing\Checkout\Model\ResourceModel\WeGetFinancingTransaction as WGFTransactionResource;
use WeGetFinancing\Checkout\Model\ResourceModel\WeGetFinancingTransaction\Collection as WGFTransactionCollection;

class UpdatePostback implements UpdatePostbackInterface
{
    use JsonStringifyResponseTrait;

    const FIELD_STATUS = "status";
    public const WGF_APPROVED_STATUS = "approved";
    public const WGF_PREAPPROVED_STATUS = "preapproved";
    public const WGF_REJECTED_STATUS = "rejected";
    public const WGF_REFUND_STATUS = "refund";
    public const VALID_STATUSES = [
        self::WGF_APPROVED_STATUS,
        self::WGF_PREAPPROVED_STATUS,
        self::WGF_REJECTED_STATUS,
        self::WGF_REFUND_STATUS
    ];

    private string $wgfStatus;

    private array $tArray;

    private OrderInterface $order;

    /**
     * UpdatePostback constructor.
     * @param LoggerInterface $logger
     * @param Session $session
     * @param Config $config
     * @param OrderRepositoryInterface $orderRepository
     * @param WeGetFinancingTransactionFactory $transactionFactory
     * @param WGFTransactionResource $transactionResource
     * @param WGFTransactionCollection $transactionCollection
     * @param CreditmemoFactory $creditMemoFactory
     * @param Invoice $invoice
     * @param CreditmemoService $creditMemoService
     * @param QuoteRepository $quoteRepository
     */
    public function __construct(
        private LoggerInterface                  $logger,
        private Session                          $session,
        private Config                           $config,
        private OrderRepositoryInterface         $orderRepository,
        private WeGetFinancingTransactionFactory $transactionFactory,
        private WGFTransactionResource           $transactionResource,
        private WGFTransactionCollection         $transactionCollection,
        private CreditmemoFactory                $creditMemoFactory,
        private Invoice                          $invoice,
        private CreditmemoService                $creditMemoService,
        private QuoteRepository                  $quoteRepository
    ) { }

    public function updatePostback(
        string $version,
        string $request_token,
        mixed  $updates,
        string $merchant_transaction_id
    ): string {
        try {
            $this->validateRequestAndSetWgfStatus($version, $request_token, $updates);
            $this->getOrder($request_token);
            $this->setOrderStatus();
            return "OK";
        } catch (Throwable $exception) {
            $this->logger->error(self::class . '::updatePostback() Exception');
            $this->logger->error($exception);
        }
        return "";
    }

    /**
     * @param string $version
     * @param string $invId
     * @param mixed $updates
     * @return void
     * @throws UpdatePostbackException
     */
    protected function validateRequestAndSetWgfStatus(
        string $version,
        string $invId,
        mixed $updates
    ): void {
        if (true === empty($version)) {
            throw new UpdatePostbackException(
                UpdatePostbackException::INVALID_REQUEST_EMPTY_VERSION_ERROR_MESSAGE,
                UpdatePostbackException::INVALID_REQUEST_EMPTY_VERSION_ERROR_CODE
            );
        }

        $apiVersion = $this->config->getApiVersion();
        if ($apiVersion != $version) {
            throw new UpdatePostbackException(
                str_replace(
                    [UpdatePostbackException::RECEIVED_API_VERSION, UpdatePostbackException::EXPECTED_API_VERSION],
                    [$version, $apiVersion],
                    UpdatePostbackException::INVALID_REQUEST_INVALID_VERSION_ERROR_MESSAGE
                ),
                UpdatePostbackException::INVALID_REQUEST_INVALID_VERSION_ERROR_CODE
            );
        }

        if (true === empty($invId)) {
            throw new UpdatePostbackException(
                UpdatePostbackException::INVALID_REQUEST_EMPTY_INV_ID_ERROR_MESSAGE,
                UpdatePostbackException::INVALID_REQUEST_EMPTY_INV_ID_ERROR_CODE
            );
        }

        if (true === empty($updates)) {
            throw new UpdatePostbackException(
                UpdatePostbackException::INVALID_REQUEST_EMPTY_UPDATES_ERROR_MESSAGE,
                UpdatePostbackException::INVALID_REQUEST_EMPTY_UPDATES_ERROR_CODE
            );
        }

        if (false === isset($updates[self::FIELD_STATUS])) {
            throw new UpdatePostbackException(
                UpdatePostbackException::INVALID_REQUEST_NOT_SET_STATUS_ERROR_MESSAGE,
                UpdatePostbackException::INVALID_REQUEST_NOT_SET_STATUS_ERROR_CODE
            );
        }

        if (true === empty($updates[self::FIELD_STATUS])) {
            throw new UpdatePostbackException(
                UpdatePostbackException::INVALID_REQUEST_EMPTY_STATUS_ERROR_MESSAGE,
                UpdatePostbackException::INVALID_REQUEST_EMPTY_STATUS_ERROR_CODE
            );
        }

        if (false === in_array($updates[self::FIELD_STATUS], self::VALID_STATUSES)) {
            throw new UpdatePostbackException(
                str_replace(
                    UpdatePostbackException::INVALID_STATUS,
                    $updates[self::FIELD_STATUS],
                    UpdatePostbackException::INVALID_REQUEST_INVALID_STATUS_ERROR_MESSAGE
                ),
                UpdatePostbackException::INVALID_REQUEST_INVALID_STATUS_ERROR_CODE
            );
        }

        $this->wgfStatus = $updates[self::FIELD_STATUS];
    }

    /**
     * @param string $invId
     * @return void
     * @throws UpdatePostbackException
     * @throws NoSuchEntityException
     */
    protected function getOrder(string $invId): void
    {
        $this->transactionCollection->addFilter('inv_id', $invId);
        $found = $this->transactionCollection->getData();

        if (count($found) >= 2) {
            throw new UpdatePostbackException(
                str_replace(
                    UpdatePostbackException::INV_ID_REPLACE,
                    $invId,
                    UpdatePostbackException::MULTIPLE_TRANSACTIONS_IN_DB_MESSAGE
                ),
                UpdatePostbackException::MULTIPLE_TRANSACTIONS_IN_DB_CODE
            );
        }

        if (true === empty($found)) {
            throw new UpdatePostbackException(
                str_replace(
                    UpdatePostbackException::INV_ID_REPLACE,
                    $invId,
                    UpdatePostbackException::TRANSACTION_NOT_FOUND_FOR_INV_ID_MESSAGE
                ),
                UpdatePostbackException::TRANSACTION_NOT_FOUND_FOR_INV_ID_CODE
            );
        }

        $this->tArray = array_pop($found);
        $quote = $this->quoteRepository->get($this->tArray['order_id']);
        $this->order = $this->orderRepository->get($quote->getReservedOrderId());
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    protected function setOrderStatus(): void
    {
        if (WgfOrderStatusInterface::STATUS_APPROVED === $this->wgfStatus) {
            $this->order->setData(OrderInterface::STATE, Order::STATE_PROCESSING);
            $this->order->setData(OrderInterface::STATUS, Order::STATE_PROCESSING);
            $this->orderRepository->save($this->order);
            return;
        }

        if (WgfOrderStatusInterface::STATUS_PRE_APPROVED === $this->wgfStatus) {
            $this->order->setData(OrderInterface::STATE, Order::STATE_PENDING_PAYMENT);
            $this->order->setData(OrderInterface::STATUS, Order::STATE_PENDING_PAYMENT);
            $this->orderRepository->save($this->order);
            return;
        }

        if (WgfOrderStatusInterface::STATUS_REJECTED === $this->wgfStatus) {
            $this->order->setData(OrderInterface::STATE, Order::STATE_CANCELED);
            $this->order->setData(OrderInterface::STATUS, Order::STATE_CANCELED);
            $this->orderRepository->save($this->order);
            return;
        }

         if (WgfOrderStatusInterface::STATUS_REFUND === $this->wgfStatus) {
             $this->refund();
         }
    }

    /**
     * @throws AlreadyExistsException
     * @throws LocalizedException
     */
    protected function refund()
    {
        if (true === (bool)$this->tArray['refunded_server_side'] ||
            true === (bool)$this->tArray['refunded_magento_side']) {
            return;
        }

        $invoices = $this->order->getInvoiceCollection();
        $invoiceIncrementId = null;
        foreach ($invoices as $invoice) {
            $invoiceIncrementId = $invoice->getIncrementId();
        }

        if (true === is_null($invoiceIncrementId)) {
            return;
        }

        $invoice = $this->invoice->loadByIncrementId($invoiceIncrementId);
        $creditMemo = $this->creditMemoFactory->createByOrder($this->order);
        $creditMemo->setInvoice($invoice);
        $this->creditMemoService->refund($creditMemo);

        $this->tArray['refunded_server_side'] = true;
        $transaction = $this->transactionFactory->create();
        $transaction->setData($this->tArray);
        $this->transactionResource->save($transaction);
    }
}
