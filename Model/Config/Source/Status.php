<?php

namespace TheMageLover\AdminCronManagement\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 * Option Source for Status
 */
class Status implements OptionSourceInterface
{
    /**
     * Get Options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Disable')],
            ['value' => 1, 'label' => __('Enable')]
        ];
    }
}
