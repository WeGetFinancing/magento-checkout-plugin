<?php declare(strict_types=1);

namespace WeGetFinancing\Checkout\Model\ResourceModel\SalesOrder;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use WeGetFinancing\Checkout\Model\SalesOrder;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(SalesOrder::class, \WeGetFinancing\Checkout\Model\ResourceModel\SalesOrder::class);
    }
}
