<?php

namespace WeGetFinancing\Checkout\Exception;

use Exception;

class FunnelUrlGeneratorException extends Exception
{
    public const MALFORMED_REQUEST_MESSAGE = 'There are errors in the request of generation of new WeGetFinancing ' .
        'Payment Funnel. The incident was reported and our technician team will work on it ASAP. ' .
        'Please try again or contact our staff.';
    public const MALFORMED_REQUEST_CODE = 1;
    public const MULTIPLE_TRANSACTIONS_IN_DB_MESSAGE = 'There was an internal error saving the transaction data. ' .
        'The incident was reported and our technician team will work on it ASAP. ' .
        'Please try again or contact our staff.';
    public const MULTIPLE_TRANSACTIONS_IN_DB_CODE = 2;
}
