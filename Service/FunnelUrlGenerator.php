<?php

namespace WeGetFinancing\Checkout\Service;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
use Psr\Log\LoggerInterface;
use Throwable;
use WeGetFinancing\Checkout\Api\FunnelUrlGeneratorInterface;
use WeGetFinancing\Checkout\Entity\Request\FunnelGeneratorRequest;
use WeGetFinancing\Checkout\Entity\Response\ExceptionJsonResponse;
use WeGetFinancing\Checkout\Exception\FunnelGeneratorRequestException;
use WeGetFinancing\Checkout\Exception\FunnelUrlGeneratorException;
use WeGetFinancing\Checkout\Exception\WGFClientException;
use WeGetFinancing\Checkout\Service\Http\WGFClient;
use WeGetFinancing\SDK\Exception\EntityValidationException;
use WeGetFinancing\Checkout\Model\WeGetFinancingTransactionFactory;
use WeGetFinancing\Checkout\Model\ResourceModel\WeGetFinancingTransaction as WeGetFinancingTransactionResource;
use WeGetFinancing\Checkout\Model\ResourceModel\WeGetFinancingTransaction\Collection as WeGetFinancingCollection;

class FunnelUrlGenerator implements FunnelUrlGeneratorInterface
{
    use JsonStringifyResponseTrait;

    /**
     * FunnelUrlGenerator constructor.
     *
     * @param LoggerInterface $logger
     * @param Session $session
     * @param FunnelGeneratorRequest $funnelGeneratorRequest
     * @param WGFClient $client
     * @param WeGetFinancingTransactionResource $resource
     * @param WeGetFinancingTransactionFactory $transactionFactory
     * @param WeGetFinancingCollection $collection
     */
    public function __construct(
        private LoggerInterface                   $logger,
        private Session                           $session,
        private FunnelGeneratorRequest            $funnelGeneratorRequest,
        private WGFClient                         $client,
        private WeGetFinancingTransactionResource $resource,
        private WeGetFinancingTransactionFactory  $transactionFactory,
        private WeGetFinancingCollection          $collection
    ) {
    }

    /**
     * Generate funnel url public not registered user
     *
     * @param string $request
     * @return string
     * @throws CouldNotSaveException
     */
    public function generateFunnelUrlPublic(string $request): string
    {
        try {
            $rawRequest = $this->decodeRequest($request);
            $rawRequest['quote'] = $this->session->getQuote();
            $this->funnelGeneratorRequest->initFromArray($rawRequest);
            $response = $this->client->getFunnel($this->funnelGeneratorRequest);
            $this->saveOrUpdateTransaction($rawRequest['quote']->getId(), $response->getData()['inv_id']);
        } catch (EntityValidationException $exception) {
            $this->logger->error(self::class . '::generateFunnelUrlPublic(): EntityValidationException');
            $this->logger->error($exception, $exception->getViolations());
            $response = new ExceptionJsonResponse($exception);
        } catch (FunnelUrlGeneratorException|WGFClientException|FunnelGeneratorRequestException $exception) {
            $response = new ExceptionJsonResponse($exception);
        } catch (Throwable $exception) {
            $this->logger->error(self::class . '::generateFunnelUrlPublic(): Unknown exception');
            $this->logger->critical($exception);
            $response = new ExceptionJsonResponse($exception);
        }
        return $this->jsonStringifyResponse($response->toArray());
    }

    /**
     * Generate funnel url registered user
     *
     * @param string $request
     * @return string
     * @throws CouldNotSaveException
     */
    public function generateFunnelUrlRegistered(string $request): string
    {
        try {
            $rawRequest = $this->decodeRequest($request);
            $quote = $this->session->getQuote();
            $this->funnelGeneratorRequest->initFromArrayAndSession($rawRequest, $quote);
            $response = $this->client->getFunnel($this->funnelGeneratorRequest);
            $this->saveOrUpdateTransaction($rawRequest['quote']->getId(), $response->getData()['inv_id']);
        } catch (EntityValidationException $exception) {
            $this->logger->error(self::class . '::generateFunnelUrlRegistered(): EntityValidationException');
            $this->logger->error($exception, $exception->getViolations());
            $response = new ExceptionJsonResponse($exception);
        } catch (FunnelUrlGeneratorException|WGFClientException|FunnelGeneratorRequestException $exception) {
            $response = new ExceptionJsonResponse($exception);
        } catch (Throwable $exception) {
            $this->logger->error(self::class . '::generateFunnelUrlRegistered(): Unknown exception');
            $this->logger->critical($exception);
            $response = new ExceptionJsonResponse($exception);
        }
        return $this->jsonStringifyResponse($response->toArray());
    }

    /**
     * Decode Request
     *
     * @param string $json
     * @return array
     * @throws FunnelUrlGeneratorException
     */
    protected function decodeRequest(string $json): array
    {
        $request = json_decode($json, true);
        $error = json_last_error();
        if (JSON_ERROR_NONE !== $error) {
            $this->logger->error(self::class . '::decodeRequest(): Malformed json request with error id ' . $error);
            $this->logger->error('Original Json: ' . $json);
            throw new FunnelUrlGeneratorException(
                FunnelUrlGeneratorException::MALFORMED_REQUEST_MESSAGE,
                FunnelUrlGeneratorException::MALFORMED_REQUEST_CODE
            );
        }
        return $request;
    }

    /**
     * Save or Update transaction
     *
     * @param int $quoteId
     * @param string $invId
     * @return void
     * @throws AlreadyExistsException
     * @throws FunnelUrlGeneratorException
     */
    protected function saveOrUpdateTransaction(int $quoteId, string $invId): void
    {
        $this->collection->addFilter('order_id', $quoteId);
        $found = $this->collection->getData();

        if (count($found) >= 2) {
            $this->logger->error(self::class . '::saveOrUpdateTransaction() Error');
            $this->logger->error('Multiple definition of order_id in the wegetfinancing transaction table');
            $this->logger->error('order_id: ' . $quoteId . ' - inv_id: ' . $invId);
            throw new FunnelUrlGeneratorException(
                FunnelUrlGeneratorException::MULTIPLE_TRANSACTIONS_IN_DB_MESSAGE,
                FunnelUrlGeneratorException::MULTIPLE_TRANSACTIONS_IN_DB_CODE
            );
        }

        $data = (true === empty($found))
            ? [
                'wegetfinancing_transaction_id' => null,
                'order_id' => $quoteId,
                'inv_id' => $invId
            ]
            : $this->updateFoundTransaction($found, $invId);

        $transaction = $this->transactionFactory->create();
        $transaction->setData($data);
        $this->resource->save($transaction);
    }

    /**
     * Update found transaction
     *
     * @param array $found
     * @param string $invId
     * @return array
     */
    private function updateFoundTransaction(array $found, string $invId): array
    {
        $data = array_pop($found);
        $data['inv_id'] = $invId;
        return $data;
    }
}
