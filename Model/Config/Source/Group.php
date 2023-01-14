<?php

namespace TheMageLover\AdminCronManagement\Model\Config\Source;

use Magento\Cron\Model\ConfigInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Group
 * Option Source for Group
 */
class Group implements OptionSourceInterface
{
    /** @var array */
    protected $options;

    /** @var ConfigInterface */
    protected $cronConfig;

    /**
     * @param ConfigInterface $cronConfig
     */
    public function __construct(ConfigInterface $cronConfig)
    {
        $this->cronConfig = $cronConfig;
    }

    /**
     * Get Options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options == null) {
            foreach (array_keys($this->cronConfig->getJobs()) as $group) {
                $this->options[] = ['label' => $group, 'value' => $group];
            }
        }

        return $this->options;
    }
}
