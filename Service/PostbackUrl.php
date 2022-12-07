<?php

namespace WeGetFinancing\Checkout\Service;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Sales\Model\Order;
use WeGetFinancing\Checkout\Api\SetOrderInvIdInterface;
use Magento\Checkout\Model\Session;
use Psr\Log\LoggerInterface;
use WeGetFinancing\Checkout\Entity\Response\ExceptionJsonResponse;
use Throwable;
use Magento\Sales\Api\OrderRepositoryInterface;
use WeGetFinancing\Checkout\Entity\Response\JsonResponse;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;

class PostbackUrl implements SetOrderInvIdInterface
{
    use JsonStringifyResponseTrait;

    private LoggerInterface $logger;

    private Session $session;

    private OrderRepositoryInterface $orderRepository;

    private string $status;

    private string $quoteId;

    private string $invId;

    /**
     * FunnelUrlGenerator constructor.
     * @param LoggerInterface $logger
     * @param Session $session
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        LoggerInterface $logger,
        Session $session,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->logger = $logger;
        $this->session = $session;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @throws CouldNotSaveException
     */
    public function setOrderInvId(string $request): string
    {
        try {
            $requestArray = json_decode($request, true);
            $this->quoteId = $requestArray['merchant_transaction_id'];
            $this->status = $requestArray['updates']['status']
            $this->invId = $requestArray['request_token'];

            $response = (new JsonResponse())->setType(JsonResponse::TYPE_SUCCESS);
        } catch (Throwable $exception) {
            $this->logger->critical($exception);
            $response = new ExceptionJsonResponse($exception);
        }

        return $this->jsonStringifyResponse($response->toArray());
    }

    protected function setStatus()
    {
        if ('approved' === $this->status) {
            $order->setData(OrderInterface::STATE, Order::STATE_PENDING_PAYMENT);
            $order->setData(OrderInterface::STATUS, Order::STATE_PENDING_PAYMENT);
            return;
        }

        if ('preapproved' === $this->status) {
            $order->setData(OrderInterface::STATE, Order::STATE_PENDING_PAYMENT);
            $order->setData(OrderInterface::STATUS, Order::STATE_PENDING_PAYMENT);
            return;
        }

        if ('rejected' === $this->status) {
            $order->setData(OrderInterface::STATE, Order::STATE_PENDING_PAYMENT);
            $order->setData(OrderInterface::STATUS, Order::STATE_PENDING_PAYMENT);
            return;
        }

        if ('refund' === $this->status) {
            $order->setData(OrderInterface::STATE, Order::STATE_PENDING_PAYMENT);
            $order->setData(OrderInterface::STATUS, Order::STATE_PENDING_PAYMENT);
            return;
        }
    }
}
