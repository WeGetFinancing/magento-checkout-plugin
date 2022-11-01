<?php

namespace WeGetFinancing\Checkout\Exception;

use Exception;

class SessionException extends Exception
{
    public const QUOTE_NOT_FOUND_ERROR_MESSAGE = 'Quote not found';
}
