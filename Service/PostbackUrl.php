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
use Magento\Quote\Api\CartRepositoryInterface;
use WeGetFinancing\Checkout\ValueObject\WgfOrderStatusInterface;

class PostbackUrl implements SetOrderInvIdInterface
{
    use JsonStringifyResponseTrait;

    private LoggerInterface $logger;

    private Session $session;

    private OrderRepositoryInterface $orderRepository;

    private CartRepositoryInterface $quoteRepository;

    private string $wgfStatus;

    private OrderInterface $order;

    /**
     * FunnelUrlGenerator constructor.
     * @param LoggerInterface $logger
     * @param Session $session
     * @param OrderRepositoryInterface $orderRepository
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        LoggerInterface $logger,
        Session $session,
        OrderRepositoryInterface $orderRepository,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->logger = $logger;
        $this->session = $session;
        $this->orderRepository = $orderRepository;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @throws CouldNotSaveException
     */
    public function setOrderInvId(string $request): string
    {
        try {
            $requestArray = json_decode($request, true);
            $this->wgfStatus = $requestArray['updates']['status'];

            $quoteId = $requestArray['merchant_transaction_id'];
            $quote = $this->quoteRepository->get($quoteId);
            $orderId = $quote->getOrigOrderId();
            $this->order = $this->orderRepository->get($orderId);

            $orderInvId = $this->order->getExtensionAttributes()->getInvId();
            if ($requestArray['request_token'] != $orderInvId) {
                throw new \Exception(
                    "Order entity id " . $this->order->getEntityId() . " with invId " . $orderInvId .
                    " doesn't match with request token invId " . $requestArray['request_token']
                );
            }
            $response = $this->setStatusAndGetResponse();
        } catch (Throwable $exception) {
            $this->logger->critical($exception);
            $response = new ExceptionJsonResponse($exception);
        }

        return $this->jsonStringifyResponse($response->toArray());
    }

    protected function setStatusAndGetResponse(): JsonResponse
    {
        if (WgfOrderStatusInterface::STATUS_APPROVED === $this->wgfStatus) {
            $this->order->setData(OrderInterface::STATE, Order::STATE_PROCESSING);
            $this->order->setData(OrderInterface::STATUS, Order::STATE_PROCESSING);
        }

        if (WgfOrderStatusInterface::STATUS_PRE_APPROVED === $this->wgfStatus) {
            $this->order->setData(OrderInterface::STATE, Order::STATE_PENDING_PAYMENT);
            $this->order->setData(OrderInterface::STATUS, Order::STATE_PENDING_PAYMENT);
        }

        if (WgfOrderStatusInterface::STATUS_REJECTED === $this->wgfStatus) {
            $this->order->setData(OrderInterface::STATE, Order::STATE_CANCELED);
            $this->order->setData(OrderInterface::STATUS, Order::STATE_CANCELED);
        }

//        if (WgfOrderStatusInterface::STATUS_REFUND === $this->wgfStatus) {
//
//        }

        return (new JsonResponse())->setType(JsonResponse::TYPE_SUCCESS);
    }
}
