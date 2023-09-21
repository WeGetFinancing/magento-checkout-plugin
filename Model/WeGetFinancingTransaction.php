<?php declare(strict_types=1);

namespace WeGetFinancing\Checkout\Model;

use Magento\Framework\Model\AbstractModel;
use WeGetFinancing\Checkout\Model\ResourceModel\WeGetFinancingTransaction as WeGetFinancingTransactionResource;

class WeGetFinancingTransaction extends AbstractModel
{
    /**
     * WeGetFinancingTransaction secondary constructor.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(WeGetFinancingTransactionResource::class);
    }
}
