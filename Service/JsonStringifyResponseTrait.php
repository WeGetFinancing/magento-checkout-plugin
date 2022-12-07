<?php

namespace WeGetFinancing\Checkout\Service;

use Magento\Framework\Exception\CouldNotSaveException;
use WeGetFinancing\Checkout\Exception\ApiResponseJsonEncodeException;

trait JsonStringifyResponseTrait
{
    /**
     * @throws CouldNotSaveException
     */
    public function jsonStringifyResponse(array $response): string
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
