<?php

namespace WeGetFinancing\Checkout\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

class UpdateOrderHandler implements HandlerInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var OrderSender
     */
    private $orderSender;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * UpdateOrderCommand constructor.
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderSender $orderSender
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderSender $orderSender,
        LoggerInterface $logger
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderSender = $orderSender;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        /** @var Payment $payment */
        $payment = $handlingSubject['payment']->getPayment();

        $baseTotalDue = $payment->getOrder()->getBaseTotalDue();
        $payment->registerCaptureNotification($baseTotalDue);

        if (!$payment->getOrder()->getEmailSent()) {
            try {
                $this->orderSender->send($payment->getOrder());
            } catch (\Exception $exception) {
                $this->logger->critical($exception);
            }
        }

        $this->orderRepository->save($payment->getOrder());
    }
}
