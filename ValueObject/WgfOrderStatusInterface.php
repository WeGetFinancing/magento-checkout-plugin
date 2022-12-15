<?php

namespace WeGetFinancing\Checkout\ValueObject;

interface WgfOrderStatusInterface
{
    const STATUS_APPROVED = 'approved';

    const STATUS_PRE_APPROVED = 'preapproved';

    const STATUS_REJECTED = 'rejected';

    const STATUS_REFUND = 'refund';
}
