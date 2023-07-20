<?php

namespace WeGetFinancing\Checkout\ValueObject\Option;

use Magento\Framework\Data\OptionSourceInterface;

class PpePosition implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'center', 'label' => __('Center')],
            ['value' => 'flex-start', 'label' => __('Flex Start')],
            ['value' => 'flex-end', 'label' => __('Flex End')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'center' => __('Center'),
            'flex-start' => __('Flex Start'),
            'flex-end' => __('Flex End')
        ];
    }
}
