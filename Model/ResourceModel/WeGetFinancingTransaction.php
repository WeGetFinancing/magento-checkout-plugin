<?php declare(strict_types=1);

namespace WeGetFinancing\Checkout\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class WeGetFinancingTransaction extends AbstractDb
{
    const TABLE_NAME = 'wegetfinancing_transaction';
    const PRIMARY_KEY = 'wegetfinancing_transaction_id';

    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, self::PRIMARY_KEY);
    }
}
