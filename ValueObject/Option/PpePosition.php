<?php

namespace WeGetFinancing\Checkout\ValueObject\Option;

use Magento\Framework\Data\OptionSourceInterface;

class PpePosition implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'center', 'label' => __('Center')],
            ['value' => 'equalSpacing', 'label' => __('Equal Spacing')],
            ['value' => 'leading', 'label' => __('Leading')],
            ['value' => 'trailing', 'label' => __('Trailing')],
            ['value' => 'fill', 'label' => __('Fill')],
            ['value' => 'fillEvenly', 'label' => __('Fill Evenly')]
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
            'equalSpacing' => __('Equal Spacing'),
            'leading' => __('Leading'),
            'trailing' => __('Trailing'),
            'fill' => __('Fill'),
            'fillEvenly' => __('Fill Evenly')
        ];
    }
}
