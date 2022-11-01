<?php

namespace WeGetFinancing\Checkout\Exception;

use Exception;

class CartItemException extends Exception
{
    public const CART_INIT_WITH_INVALID_DATA_ERROR_MESSAGE = 'Error collecting product data from quote';
    public const CART_INIT_WITH_INVALID_DATA_ERROR_CODE = 1;
}
