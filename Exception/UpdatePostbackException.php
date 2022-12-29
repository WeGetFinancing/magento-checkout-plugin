<?php

namespace WeGetFinancing\Checkout\Exception;

use Exception;

class UpdatePostbackException extends Exception
{
    public const EXPECTED_API_VERSION = "__EXPECTED_API_VERSION__";
    public const RECEIVED_API_VERSION = "__RECEIVED_API_VERSION__";
    public const INVALID_STATUS = "__INVALID_STATUS__";

    public const INVALID__WGF_STATUS = "__INVALID__WGF_STATUS__";

    public const INVALID_REQUEST_EMPTY_VERSION_ERROR_MESSAGE = 'UpdatePostback invalid request, empty version';
    public const INVALID_REQUEST_EMPTY_VERSION_ERROR_CODE = 1;

    public const INVALID_REQUEST_INVALID_VERSION_ERROR_MESSAGE =
        'UpdatePostback invalid request, version ' . self::RECEIVED_API_VERSION .
        ' does not match with the configuration one ' . self::EXPECTED_API_VERSION;
    public const INVALID_REQUEST_INVALID_VERSION_ERROR_CODE = 2;

    public const INVALID_REQUEST_EMPTY_INV_ID_ERROR_MESSAGE = 'UpdatePostback invalid request, empty inv Id';
    public const INVALID_REQUEST_EMPTY_INV_ID_ERROR_CODE = 3;

    public const INVALID_REQUEST_EMPTY_UPDATES_ERROR_MESSAGE = 'UpdatePostback invalid request, empty updates';
    public const INVALID_REQUEST_EMPTY_UPDATES_ERROR_CODE = 4;

    public const INVALID_REQUEST_NOT_SET_STATUS_ERROR_MESSAGE = 'UpdatePostback invalid request, status is not set';
    public const INVALID_REQUEST_NOT_SET_STATUS_ERROR_CODE = 5;

    public const INVALID_REQUEST_EMPTY_STATUS_ERROR_MESSAGE = 'UpdatePostback invalid request, empty status';
    public const INVALID_REQUEST_EMPTY_STATUS_ERROR_CODE = 6;

    public const INVALID_REQUEST_INVALID_STATUS_ERROR_MESSAGE =
        'UpdatePostback invalid request, invalid status ' . self::INVALID_STATUS;
    public const INVALID_REQUEST_INVALID_STATUS_ERROR_CODE = 7;

    public const INVALID_REQUEST_EMPTY_QUOTE_ID_ERROR_MESSAGE = 'UpdatePostback invalid request, empty quote id';
    public const INVALID_REQUEST_EMPTY_QUOTE_ID_ERROR_CODE = 8;

    public const INVALID_WGF_STATUS_ERROR_MESSAGE = 'UpdatePostback invalid wgf status';
    public const INVALID_WGF_STATUS_ERROR_CODE = 9;
}
