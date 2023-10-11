<?php

namespace WeGetFinancing\Checkout\ValueObject;

interface WgfOrderStatusInterface
{
    public const STATUS_APPROVED = 'approved';
    public const STATUS_PRE_APPROVED = 'preapproved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_REFUND = 'refund';
}
