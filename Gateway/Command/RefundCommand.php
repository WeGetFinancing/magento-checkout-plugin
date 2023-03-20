<?php

namespace WeGetFinancing\Checkout\Gateway\Command;

use Magento\Payment\Gateway\CommandInterface;

class RefundCommand implements CommandInterface
{
    /**
     * @inheritdoc
     */
    public function execute(array $commandSubject)
    { }
}
