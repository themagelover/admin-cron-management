<?php

namespace TheMageLover\AdminCronManagement\Controller\Adminhtml\Jobs;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use TheMageLover\AdminCronManagement\Model\ResourceModel\ScheduledJobs\Collection;
use TheMageLover\AdminCronManagement\Model\ResourceModel\ScheduledJobs\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassDelete
 * Mass Delete action for Scheduled Jobs Listing
 */
class MassDelete extends Action
{
    /** @var Filter */
    protected $filter;

    /** @var CollectionFactory */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory)
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        // Get records
        /** @var Collection $collection */
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $count = $collection->getSize();

        // Delete records
        foreach ($collection as $job) {
            $job->delete();
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $count));
        return $this->_redirect('*/*/');
    }
}
