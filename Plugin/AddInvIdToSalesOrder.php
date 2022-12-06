<?php declare(strict_types=1);

namespace WeGetFinancing\Checkout\Plugin;

use WeGetFinancing\Checkout\Model\ResourceModel\SalesOrder\Collection;
use WeGetFinancing\Checkout\Model\ResourceModel\SalesOrder\CollectionFactory;
use Magento\Sales\Api\OrderRepositoryInterface;

class AddInvIdToSalesOrder
{
    private CollectionFactory $wgfSalesOrderCollectionFactory;

    /**
     * AddInvIdToSalesOrder constructor.
     * @param CollectionFactory $wgfSalesOrderCollectionFactory
     */
    public function __construct(
        CollectionFactory $wgfSalesOrderCollectionFactory
    ) {
        $this->wgfSalesOrderCollectionFactory = $wgfSalesOrderCollectionFactory;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param $result
     * @return mixed
     */
    public function afterGet(
        OrderRepositoryInterface $subject,
                                 $result
    ) {
        /** @var Collection $wgfSalesOrder */
        $wgfSalesOrderCollection = $this->wgfSalesOrderCollectionFactory->create();
        $wgfSalesOrder = $wgfSalesOrderCollection
            ->addFieldToFilter('order_id', $result->getId())
            ->getFirstItem();

        $extensionAttributes = $result->getExtensionAttributes();

        $extensionAttributes->setData('inv_id', $wgfSalesOrder->getData('inv_id'));

        $result->setExtensionAttributes($extensionAttributes);

        return $result;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param $result
     * @return mixed
     */
    public function afterGetList(
        OrderRepositoryInterface $subject,
                                 $result
    ) {
        foreach ($result->getItems() as $order) {
            $this->afterGet($subject, $order);
        }

        return $result;
    }
}
