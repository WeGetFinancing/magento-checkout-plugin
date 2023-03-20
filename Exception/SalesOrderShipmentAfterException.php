<?php

namespace WeGetFinancing\Checkout\Exception;

use Exception;

class SalesOrderShipmentAfterException extends Exception
{
    public const ORDER_ID_REPLACE = "__ORDER_ID__";

    public const MULTIPLE_TRANSACTIONS_IN_DB_MESSAGE = 'Multiple transaction for order_id: ' . self::ORDER_ID_REPLACE;
    public const MULTIPLE_TRANSACTIONS_IN_DB_CODE = 1;
}
