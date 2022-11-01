<?php

namespace WeGetFinancing\Checkout\Exception;

use Exception;

class AddressEntityException extends Exception
{
    public const STATE_FROM_REGION_ID_NOT_FOUND_ERROR_MESSAGE = 'State code not found.';
    public const STATE_FROM_REGION_ID_NOT_FOUND_ERROR_CODE = 1;
}
