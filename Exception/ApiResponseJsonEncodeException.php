<?php

namespace WeGetFinancing\Checkout\Exception;

use Exception;

final class ApiResponseJsonEncodeException extends Exception
{
    public const JSON_ENCODE_GENERAL_ERROR_MESSAGE = 'Response cannot be encoded';
}
