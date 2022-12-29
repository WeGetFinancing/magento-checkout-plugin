<?php

namespace WeGetFinancing\Checkout\Service;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\CouldNotSaveException;
use Psr\Log\LoggerInterface;
use Throwable;
use WeGetFinancing\Checkout\Api\FunnelUrlGeneratorInterface;
use WeGetFinancing\Checkout\Entity\Request\FunnelGeneratorRequest;
use WeGetFinancing\Checkout\Entity\Response\ExceptionJsonResponse;
use WeGetFinancing\Checkout\Service\Http\Client;
use WeGetFinancing\SDK\Exception\EntityValidationException;

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
    ) {
        $this->logger = $logger;
        $this->session = $session;
        $this->funnelGeneratorRequest = $funnelGeneratorRequest;
        $this->client = $client;
    }

    /**
     * {@inheritDoc}
     * @throws CouldNotSaveException
     */
    public function generateFunnelUrlPublic($request): string
    {
        try {
            $rawRequest = json_decode($request, true);
            $rawRequest['quote'] = $this->session->getQuote();
            $this->funnelGeneratorRequest->initFromArray($rawRequest);

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

    /**
     * {@inheritDoc}
     * @throws CouldNotSaveException
     */
    public function generateFunnelUrlRegistered($request): string
    {
        try {
            $rawRequest = json_decode($request, true);
            $quote = $this->session->getQuote();

            $this->logger->critical('QUOTE ID: ' . $quote->getId())
            ;

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

