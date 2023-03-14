<?php declare(strict_types=1);

namespace WeGetFinancing\Checkout\Model\ResourceModel\SalesOrder;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use WeGetFinancing\Checkout\Model\WeGetFinancingTransaction;
use WeGetFinancing\Checkout\Model\ResourceModel\WeGetFinancingTransaction as WeGetFinancingTransactionResource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(WeGetFinancingTransaction::class, WeGetFinancingTransactionResource::class);
    }
}
