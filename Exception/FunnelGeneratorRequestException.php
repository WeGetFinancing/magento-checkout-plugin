<?php

namespace WeGetFinancing\Checkout\Exception;

use Exception;

class FunnelGeneratorRequestException extends Exception
{
    public const FIELD_CONST = '%FIELD%';
    public const EMPTY_MANDATORY_FIELD_ERROR_MESSAGE = 'The field %FIELD% is mandatory.';
    public const INVALID_REQUEST_ERROR_MESSAGE = 'Invalid request.';
    public const INVALID_REQUEST_ERROR_CODE = 1;
    public const QUOTE_NOT_FOUND_ERROR_MESSAGE = 'Quote not found.';
    public const QUOTE_NOT_FOUND_ERROR_CODE = 2;
    public const QUOTE_WITH_NO_ITEMS_ERROR_MESSAGE = 'No product defined in the quote.';
    public const QUOTE_WITH_NO_ITEMS_ERROR_CODE = 3;
}
