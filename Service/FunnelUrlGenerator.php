<?php

namespace WeGetFinancing\Checkout\Service;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\CouldNotSaveException;
use Psr\Log\LoggerInterface;
use Throwable;
use WeGetFinancing\Checkout\Api\FunnelUrlGeneratorInterface;
use WeGetFinancing\Checkout\Entity\Request\FunnelGeneratorRequest;
use WeGetFinancing\Checkout\Entity\Response\ExceptionJsonResponse;
use WeGetFinancing\Checkout\Exception\ApiResponseJsonEncodeException;
use WeGetFinancing\Checkout\Service\Http\Client;
use WeGetFinancing\SDK\Exception\EntityValidationException;

class FunnelUrlGenerator implements FunnelUrlGeneratorInterface
{
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

    /**
     * @throws CouldNotSaveException
     */
    protected function jsonStringifyResponse(array $response): string
    {
        $stringResponse = json_encode($response);

        if (false === $stringResponse) {
            $exception = new ApiResponseJsonEncodeException(
                json_last_error_msg(),
                json_last_error()
            );

            throw new CouldNotSaveException(
                __(ApiResponseJsonEncodeException::JSON_ENCODE_GENERAL_ERROR_MESSAGE),
                $exception
            );
        }

        return $stringResponse;
    }
}

