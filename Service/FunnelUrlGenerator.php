<?php

namespace WeGetFinancing\Checkout\Service;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Model\Quote;
use Psr\Log\LoggerInterface;
use Throwable;
use WeGetFinancing\Checkout\Api\FunnelUrlGeneratorInterface;
use WeGetFinancing\Checkout\Entity\Request\FunnelGeneratorRequest;
use WeGetFinancing\Checkout\Entity\Response\ExceptionJsonResponse;
use WeGetFinancing\Checkout\Entity\Response\JsonResponse;
use WeGetFinancing\Checkout\Service\Http\Client;
use WeGetFinancing\SDK\Exception\EntityValidationException;
use WeGetFinancing\Checkout\Model\WeGetFinancingTransactionFactory;
use WeGetFinancing\Checkout\Model\ResourceModel\WeGetFinancingTransaction as WeGetFinancingTransactionResource;

class FunnelUrlGenerator implements FunnelUrlGeneratorInterface
{
    use JsonStringifyResponseTrait;

    private LoggerInterface $logger;

    private Session $session;

    private FunnelGeneratorRequest $funnelGeneratorRequest;

    private Client $client;

    /**
     * FunnelUrlGenerator constructor.
     * @param LoggerInterface $logger
     * @param Session $session
     * @param FunnelGeneratorRequest $funnelGeneratorRequest
     * @param Client $client
     */
    public function __construct(
        LoggerInterface $logger,
        Session $session,
        FunnelGeneratorRequest $funnelGeneratorRequest,
        Client $client,
        private WeGetFinancingTransactionResource $resource,
        private WeGetFinancingTransactionFactory $transactionFactory
    ) {
        $this->logger = $logger;
        $this->session = $session;
        $this->funnelGeneratorRequest = $funnelGeneratorRequest;
        $this->client = $client;
    }

    /**
     * {@inheritDoc}
     * @throws CouldNotSaveException
     * @throws AlreadyExistsException
     */
    public function generateFunnelUrlPublic($request): string
    {
        try {
            $rawRequest = json_decode($request, true);
            $rawRequest['quote'] = $this->session->getQuote();
            $this->funnelGeneratorRequest->initFromArray($rawRequest);
            $response = $this->client->execute($this->funnelGeneratorRequest);
            if (JsonResponse::TYPE_SUCCESS === $response->getType()) {
                $transaction = $this->transactionFactory->create();
                $data = [
                    'wegetfinancing_transaction_id' => null,
                    'order_id' => $rawRequest['quote']->getId(),
                    'inv_id' => $response->getData()['invId']
                ];
                $transaction->setData($data);
                $this->resource->save($transaction);
            }
        } catch (EntityValidationException $exception) {
            $this->logger->critical($exception, $exception->getViolations());
            $response = new ExceptionJsonResponse($exception);
        } catch (Throwable $exception) {
            $this->logger->critical($exception);
            $response = new ExceptionJsonResponse($exception);
        }

        return $this->jsonStringifyResponse($response->toArray());
    }

    /**
     * {@inheritDoc}
     * @throws CouldNotSaveException
     */
    public function generateFunnelUrlRegistered($request): string
    {
        try {
            $rawRequest = json_decode($request, true);
            $quote = $this->session->getQuote();

            $this->logger->critical('QUOTE ID: ' . $quote->getId());

            $this->funnelGeneratorRequest->initFromArrayAndSession($rawRequest, $quote);
            $response = $this->client->execute($this->funnelGeneratorRequest);
        } catch (EntityValidationException $exception) {
            $this->logger->critical($exception, $exception->getViolations());
            $response = new ExceptionJsonResponse($exception);
        } catch (Throwable $exception) {
            $this->logger->critical($exception);
            $response = new ExceptionJsonResponse($exception);
        }

        return $this->jsonStringifyResponse($response->toArray());
    }
}

