<?php

namespace WeGetFinancing\Checkout\Service;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;
use Magento\Checkout\Model\Session;
use Psr\Log\LoggerInterface;
use WeGetFinancing\Checkout\Api\UpdatePostbackInterface;
use WeGetFinancing\Checkout\Entity\Response\ExceptionJsonResponse;
use Throwable;
use Magento\Sales\Api\OrderRepositoryInterface;
use WeGetFinancing\Checkout\Entity\Response\JsonResponse;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use WeGetFinancing\Checkout\Exception\JsonResponseException;
use WeGetFinancing\Checkout\ValueObject\WgfOrderStatusInterface;
use WeGetFinancing\Checkout\Gateway\Config;
use WeGetFinancing\Checkout\Exception\UpdatePostbackException;

class UpdatePostback implements UpdatePostbackInterface
{
    const FIELD_STATUS = "status";

    const VALID_STATUSES = [ "approved", "preapproved", "rejected", "refund" ];

    use JsonStringifyResponseTrait;

    private LoggerInterface $logger;

    private Session $session;

    private Config $config;

    private OrderRepositoryInterface $orderRepository;

    private CartRepositoryInterface $quoteRepository;

    private string $wgfStatus;

    private OrderInterface $order;

    /**
     * FunnelUrlGenerator constructor.
     * @param LoggerInterface $logger
     * @param Session $session
     * @param Config $config
     * @param OrderRepositoryInterface $orderRepository
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        LoggerInterface $logger,
        Session $session,
        Config $config,
        OrderRepositoryInterface $orderRepository,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->logger = $logger;
        $this->session = $session;
        $this->config = $config;
        $this->orderRepository = $orderRepository;
        $this->quoteRepository = $quoteRepository;
    }

    public function updatePostback(
        string $version,
        string $request_token,
        mixed $updates,
        string $merchant_transaction_id
    ): string {
        try {
            $this->validateRequestAndSetData($version, $request_token, $updates, $merchant_transaction_id);
            $this->setOrderStatus();
            return "OK";
        } catch (Throwable $exception) {
            $this->logger->critical($exception);
        }
        return "";
    }

    /**
     * @param string $version
     * @param string $invId
     * @param mixed $updates
     * @param string $quoteId
     * @return void
     * @throws NoSuchEntityException
     * @throws UpdatePostbackException
     */
    protected function validateRequestAndSetData(
        string $version,
        string $invId,
        mixed $updates,
        string $quoteId
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

        if (true === empty($quoteId)) {
            throw new UpdatePostbackException(
                UpdatePostbackException::INVALID_REQUEST_EMPTY_QUOTE_ID_ERROR_MESSAGE,
                UpdatePostbackException::INVALID_REQUEST_EMPTY_QUOTE_ID_ERROR_CODE
            );
        }

        $quote = $this->quoteRepository->get($quoteId);
        $orderId = $quote->getOrigOrderId();
        $this->order = $this->orderRepository->get($orderId);
    }

    /**
     * @return void
     * @throws UpdatePostbackException
     */
    protected function setOrderStatus()
    {
        if (WgfOrderStatusInterface::STATUS_APPROVED === $this->wgfStatus) {
            $this->order->setData(OrderInterface::STATE, Order::STATE_PROCESSING);
            $this->order->setData(OrderInterface::STATUS, Order::STATE_PROCESSING);
        } elseif (WgfOrderStatusInterface::STATUS_PRE_APPROVED === $this->wgfStatus) {
            $this->order->setData(OrderInterface::STATE, Order::STATE_PENDING_PAYMENT);
            $this->order->setData(OrderInterface::STATUS, Order::STATE_PENDING_PAYMENT);
        } elseif (WgfOrderStatusInterface::STATUS_REJECTED === $this->wgfStatus) {
            $this->order->setData(OrderInterface::STATE, Order::STATE_CANCELED);
            $this->order->setData(OrderInterface::STATUS, Order::STATE_CANCELED);
        }
//         elseif (WgfOrderStatusInterface::STATUS_REFUND === $this->wgfStatus) {
//
//        }
        else {
            throw new UpdatePostbackException(
                UpdatePostbackException::INVALID_WGF_STATUS_ERROR_MESSAGE,
                UpdatePostbackException::INVALID_WGF_STATUS_ERROR_CODE
            );
        }
        $this->orderRepository->save($this->order);
    }
}
