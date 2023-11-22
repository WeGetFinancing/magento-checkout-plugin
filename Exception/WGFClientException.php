<?php

namespace WeGetFinancing\Checkout\Exception;

use WeGetFinancing\SDK\Exception\EntityValidationException;
use \Exception;

class WGFClientException extends Exception
{
    public const MALFORMED_AUTH_ENTITY_MESSAGE = 'There are errors in the configuration of the plugin. ' .
        'The incident was reported and our technician team will work on it ASAP. '.
        'Please try again later or contact our staff.';
    public const MALFORMED_AUTH_ENTITY_CODE = 1;
    public const SERVER_RESPONSE_ERROR_MESSAGE = 'The WeGetFinancing API responded with errors. ' .
        'The incident was reported and our technician team will work on it ASAP. '.
        'Please try again later or contact our staff.';
    public const SERVER_RESPONSE_ERROR_CODE = 2;
}
