<?php

namespace WeGetFinancing\Checkout\Entity\Response;

use Throwable;
use WeGetFinancing\SDK\Exception\EntityValidationException;

class ExceptionJsonResponse extends JsonResponse
{
    /**
     * @param Throwable $exception
     */
    public function __construct(Throwable $exception)
    {
        parent::__construct([
            'type' => JsonResponse::TYPE_ERROR,
            'messages' => ($exception instanceof EntityValidationException)
                ? $exception->getViolations()
                : [ [ 'field' => 'unknown', 'message' => $exception->getMessage() ] ]
        ]);
    }
}
