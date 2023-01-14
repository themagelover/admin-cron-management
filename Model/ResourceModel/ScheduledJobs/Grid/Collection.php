<?php

namespace TheMageLover\AdminCronManagement\Model\ResourceModel\ScheduledJobs\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class Collection extends SearchResult
{
    /**
     * @var string
     */
    protected $_idFieldName = 'schedule_id';
}
