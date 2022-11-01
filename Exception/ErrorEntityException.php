<?php

namespace WeGetFinancing\Checkout\Exception;

use Exception;

class ErrorEntityException extends Exception
{
    public const INVALID_INIT_DATA_ERROR_MESSAGE = 'Error entity initialised with invalid data.';
    public const INVALID_INIT_DATA_ERROR_CODE = 1;
}
