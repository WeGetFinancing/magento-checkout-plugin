<?php declare(strict_types=1);

namespace WeGetFinancing\Checkout\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class WeGetFinancingTransaction extends AbstractDb
{
    public const TABLE_NAME = 'wegetfinancing_transaction';
    public const PRIMARY_KEY = 'wegetfinancing_transaction_id';

    /**
     * WeGetFinancingTransaction secondary constructor.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(self::TABLE_NAME, self::PRIMARY_KEY);
    }
}
