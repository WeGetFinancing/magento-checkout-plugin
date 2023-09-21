<?php declare(strict_types=1);

namespace WeGetFinancing\Checkout\Model\ResourceModel\WeGetFinancingTransaction;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use WeGetFinancing\Checkout\Model\WeGetFinancingTransaction;
use WeGetFinancing\Checkout\Model\ResourceModel\WeGetFinancingTransaction as WeGetFinancingTransactionResource;

class Collection extends AbstractCollection
{
    /**
     * Collection secondary constructor.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(WeGetFinancingTransaction::class, WeGetFinancingTransactionResource::class);
    }
}
