<?php

namespace TheMageLover\AdminCronManagement\Controller\Adminhtml\Executor;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Cache\TypeListInterface;
use TheMageLover\AdminCronManagement\Helper\Data;

/**
 * Class MassUpdateStatus
 * Mass Update Action for Job Executor
 */
class MassUpdateStatus extends Action
{
    /** @var Data */
    protected $helperData;

    /** @var TypeListInterface */
    protected $cacheTypeList;

    /**
     * @param Context $context
     * @param Data $helperData
     * @param TypeListInterface $cacheTypeList
     */
    public function __construct(Context $context, Data $helperData, TypeListInterface $cacheTypeList)
    {
        $this->helperData = $helperData;
        $this->cacheTypeList = $cacheTypeList;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            // Get selected records
            $params = $this->getRequest()->getParams();
            $jobSelected = $this->helperData->getSelectedRecords($params);
            $count = 0;

            // Change config each job
            $status = (int)$this->getRequest()->getParam('status');
            foreach ($jobSelected as $jobName) {
                $flag = $this->helperData->updateJobStatus($jobName, $status);
                if ($flag) {
                    $count++;
                }
            }

            // Success message
            if ($count > 0) {
                $this->cacheTypeList->cleanType('config');
                $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $count));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $this->_redirect('*/*/');
    }
}
