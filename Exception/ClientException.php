<?php

namespace WeGetFinancing\Checkout\Exception;

use WeGetFinancing\SDK\Exception\EntityValidationException;

class ClientException extends EntityValidationException
{
    public const SERVER_RESPONSE_ERROR_MESSAGE = 'Server responded with errors.';
    public const SERVER_RESPONSE_ERROR_CODE = 1;
    public const UNEXPECTED_SERVER_ERROR_MESSAGE = 'Unexpected server error.';
    public const UNEXPECTED_SERVER_ERROR_CODE = 2;
}
