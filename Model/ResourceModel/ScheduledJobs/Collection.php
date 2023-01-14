<?php

namespace TheMageLover\AdminCronManagement\Model\ResourceModel\ScheduledJobs;

class Collection extends \Magento\Cron\Model\ResourceModel\Schedule\Collection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'schedule_id';
}
