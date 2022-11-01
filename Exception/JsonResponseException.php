<?php

namespace WeGetFinancing\Checkout\Exception;

use Exception;

class JsonResponseException extends Exception
{
    public const INVALID_TYPE_ERROR_MESSAGE = 'JsonResponse set type with invalid one.';
    public const INVALID_TYPE_ERROR_CODE = 1;
    public const INIT_WITHOUT_TYPE_ERROR_MESSAGE = 'JsonResponse initialised without type.';
    public const INIT_WITHOUT_TYPE_ERROR_CODE = 2;
}
