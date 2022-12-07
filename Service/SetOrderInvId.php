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

class SetOrderInvId implements SetOrderInvIdInterface
{
    use JsonStringifyResponseTrait;

    private LoggerInterface $logger;

    private Session $session;

    private OrderRepositoryInterface $orderRepository;

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
            $order = $this->session->getLastRealOrder();
            $ext = $order->getExtensionAttributes();
            $ext->setInvId($requestArray['invId']);
            $order->setExtensionAttributes($ext);
            $this->orderRepository->save($order);
            $response = (new JsonResponse())->setType(JsonResponse::TYPE_SUCCESS);
        } catch (Throwable $exception) {
            $this->logger->critical($exception);
            $response = new ExceptionJsonResponse($exception);
        }

        return $this->jsonStringifyResponse($response->toArray());
    }
}
